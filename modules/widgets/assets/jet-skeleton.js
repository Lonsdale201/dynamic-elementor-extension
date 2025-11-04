(function ($) {
  "use strict";

  const TARGET_SELECTOR =
    '.skeleton-multitext-loading.elementor-widget-text-editor, .skeleton-multitext-loading[data-widget_type="jet-listing-dynamic-field.default"], .skeleton-multitext-loading.elementor-widget-jet-listing-dynamic-field';
  const OVERLAY_CLASS = "hw-skeleton-overlay";
  const LINE_CLASS = "hw-skeleton-overlay__line";
  const RESIZE_DEBOUNCE = 150;
  const GRID_OBSERVER_KEY = "__hwSkeletonGridObserver";
  let HW_SKELETON_DEV = false; // set true to keep shimmer always on for testing
  const SKELETON_STYLE_PROPS = [
    {
      style: "position",
      dataKey: "hwSkeletonOriginalPosition",
      inlineKey: "hwSkeletonInlinePosition",
      skipValues: ["", "static"],
    },
    {
      style: "top",
      dataKey: "hwSkeletonOriginalTop",
      inlineKey: "hwSkeletonInlineTop",
      skipValues: ["", "auto"],
    },
    {
      style: "right",
      dataKey: "hwSkeletonOriginalRight",
      inlineKey: "hwSkeletonInlineRight",
      skipValues: ["", "auto"],
    },
    {
      style: "bottom",
      dataKey: "hwSkeletonOriginalBottom",
      inlineKey: "hwSkeletonInlineBottom",
      skipValues: ["", "auto"],
    },
    {
      style: "left",
      dataKey: "hwSkeletonOriginalLeft",
      inlineKey: "hwSkeletonInlineLeft",
      skipValues: ["", "auto"],
    },
    {
      style: "width",
      dataKey: "hwSkeletonOriginalWidth",
      inlineKey: "hwSkeletonInlineWidth",
      skipValues: ["", "auto"],
      useRect: true,
    },
    {
      style: "height",
      dataKey: "hwSkeletonOriginalHeight",
      inlineKey: "hwSkeletonInlineHeight",
      skipValues: ["", "auto"],
      useRect: true,
    },
    {
      style: "zIndex",
      dataKey: "hwSkeletonOriginalZindex",
      inlineKey: "hwSkeletonInlineZindex",
      skipValues: ["", "auto", "0"],
    },
    {
      style: "transform",
      dataKey: "hwSkeletonOriginalTransform",
      inlineKey: "hwSkeletonInlineTransform",
      skipValues: ["", "none"],
    },
  ];

  const debounce = (fn, delay) => {
    let timerId;
    return function () {
      const context = this;
      const args = arguments;
      clearTimeout(timerId);
      timerId = setTimeout(() => fn.apply(context, args), delay);
    };
  };

  const collectTargets = (root) => {
    const $root = $(root);
    let $targets = $();
    const selector =
      '.skeleton-multitext-loading.elementor-widget-text-editor, .skeleton-multitext-loading[data-widget_type="jet-listing-dynamic-field.default"], .skeleton-multitext-loading.elementor-widget-jet-listing-dynamic-field, .skeleton-multitext-loading.elementor-widget-heading, .skeleton-multitext-loading[data-widget_type="heading.default"]';

    if ($root.is(selector)) {
      $targets = $targets.add($root);
    }

    return $targets.add($root.find(selector));
  };

  const stripExistingOverlay = ($container) => {
    $container.find(`.${OVERLAY_CLASS}`).remove();
  };

  const getLineRects = (container) => {
    if (!container) {
      return [];
    }

    const rects = [];
    const walker = document.createTreeWalker(
      container,
      NodeFilter.SHOW_TEXT,
      {
        acceptNode: (node) =>
          /\S/.test(node.nodeValue)
            ? NodeFilter.FILTER_ACCEPT
            : NodeFilter.FILTER_REJECT,
      },
      false
    );

    let node;
    while ((node = walker.nextNode())) {
      const range = document.createRange();
      range.selectNodeContents(node);
      const cr = range.getClientRects();
      for (let i = 0; i < cr.length; i++) {
        const r = cr[i];
        if (r.width > 1 && r.height > 1) {
          rects.push({
            top: r.top,
            left: r.left,
            width: r.width,
            height: r.height,
          });
        }
      }
      range.detach();
    }

    if (!rects.length) {
      return rects;
    }

    // Filter out outlier rectangles (e.g., block-level fallback rects)
    const heights = rects.map((r) => r.height).sort((a, b) => a - b);
    const median = heights[Math.floor(heights.length / 2)] || heights[0];
    const maxHeight = Math.max(12, median * 1.8);

    return rects.filter((r) => r.height <= maxHeight);
  };

  const buildOverlay = ($widget) => {
    let $container = $widget.find(".elementor-widget-container").first();

    if (!$container.length) {
      $container = $widget; // fallback to widget root
    }

    const containerEl = $container[0];
    const containerRect = containerEl.getBoundingClientRect();

    if (
      !containerRect ||
      containerRect.width === 0 ||
      containerRect.height === 0
    ) {
      return;
    }

    // Ensure positioning context for absolute overlay
    const cs = window.getComputedStyle(containerEl);
    if (cs && cs.position === "static") {
      $container.css("position", "relative");
    }

    stripExistingOverlay($container);

    const rects = getLineRects(containerEl);

    const $overlay = $("<div/>", {
      class: OVERLAY_CLASS,
      "aria-hidden": "true",
    });

    // Small bleed to compensate for subpixel rounding/letter-spacing gaps
    const BLEED_X = 1.5;
    const BLEED_Y = 0.5;
    const clamp = (v, min, max) => Math.max(min, Math.min(max, v));

    rects.forEach((rect, index) => {
      let width = rect.width;
      let height = rect.height;

      if (width <= 0.5 || height <= 0.5) {
        return;
      }

      let top = rect.top - containerRect.top;
      let left = rect.left - containerRect.left;

      // Expand a bit to cover punctuation/head/tail subpixel gaps
      top = clamp(top - BLEED_Y * 0.5, 0, containerRect.height);
      left = clamp(left - BLEED_X, 0, containerRect.width);
      width = clamp(width + BLEED_X * 2, 0, containerRect.width - left);
      height = clamp(height + BLEED_Y, 0, containerRect.height - top);

      const $line = $("<div/>", {
        class: LINE_CLASS,
      }).css({
        top: `${top}px`,
        left: `${left}px`,
        width: `${width}px`,
        height: `${height}px`,
        animationDelay: `${(index % 6) * 0.08}s`,
      });

      $overlay.append($line);
    });

    if ($overlay.children().length) {
      $container.append($overlay);
    } else {
      const $fallback = $("<div/>", {
        class: OVERLAY_CLASS,
        "aria-hidden": "true",
      }).append(
        $("<div/>", {
          class: LINE_CLASS,
        }).css({
          top: 0,
          left: 0,
          width: `${containerRect.width}px`,
          height: `${Math.max(containerRect.height, 12)}px`,
        })
      );

      $container.append($fallback);
    }

    if (HW_SKELETON_DEV) {
      forceDevOn($widget);
    }
  };

  const observeParentGrid = ($widget) => {
    const $grid = $widget.closest(".jet-listing-grid");

    if (!$grid.length) {
      return;
    }

    const node = $grid[0];

    if (node[GRID_OBSERVER_KEY]) {
      return;
    }

    const observer = new MutationObserver((mutations) => {
      let shouldUpdate = false;

      mutations.forEach((mutation) => {
        if (
          mutation.type === "attributes" &&
          mutation.attributeName === "class"
        ) {
          const classList = mutation.target.classList;

          if (
            classList.contains("jet-listing-grid-loading") ||
            classList.contains("hw-js-skeleton-loading")
          ) {
            shouldUpdate = true;
          }
        }
      });

      if (shouldUpdate) {
        scheduleUpdate($grid);
        handleGridSkeletonState($grid);
      }
    });

    observer.observe(node, {
      attributes: true,
      attributeFilter: ["class"],
    });

    node[GRID_OBSERVER_KEY] = observer;
  };

  const updateTargets = (root = document) => {
    const $targets = collectTargets(root);

    $targets.each(function () {
      const $widget = $(this);

      if (!$widget.is(":visible")) {
        return;
      }

      captureGridLayoutState($widget.closest(".jet-listing-grid"));
      observeParentGrid($widget);
      buildOverlay($widget);
      handleGridSkeletonState($widget.closest(".jet-listing-grid"));
    });
  };

  const scheduleUpdate = (root = document) => {
    requestAnimationFrame(() => updateTargets(root));
  };

  const bindEvents = () => {
    $(window).on(
      "resize",
      debounce(() => scheduleUpdate(), RESIZE_DEBOUNCE)
    );

    $(document).on(
      "jet-engine/listing/ajax-get-listing/done",
      (event, $html) => {
        scheduleUpdate($html && $html.length ? $html : document);
      }
    );

    $(document).on(
      "jet-engine/listing-grid/after-load-more",
      (event, instance) => {
        if (instance && instance.container) {
          scheduleUpdate(instance.container);
        } else {
          scheduleUpdate();
        }
      }
    );

    // JetSmartFilters event bus integration (start/end loading)
    const busCandidates = [
      window.JetSmartFilters && window.JetSmartFilters.events,
      window.JetSmartFilters && window.JetSmartFilters.eventBus,
      window.eventBus,
      window.JetSmartFilters && window.JetSmartFilters.bus,
    ].filter(Boolean);

    const bus = busCandidates.find(
      (candidate) => candidate && typeof candidate.subscribe === "function"
    );

    if (bus) {
      try {
        bus.subscribe("ajaxFilters/start-loading", (provider, queryId) => {
          const $grids = getProviderGrids(provider, queryId);

          $grids.addClass("hw-jet-listing-skeleton");
          captureGridLayoutState($grids);
          $grids.addClass("hw-js-skeleton-loading");
          applyGridLayoutState($grids);
          scheduleUpdate($grids.length ? $grids : document);
        });

        bus.subscribe("ajaxFilters/end-loading", (provider, queryId) => {
          const $grids = getProviderGrids(provider, queryId);

          $grids.removeClass("hw-js-skeleton-loading");
          restoreGridLayoutState($grids);
          scheduleUpdate($grids.length ? $grids : document);
        });
      } catch (e) {
        // noop if bus behaves differently
      }
    }
  };

  function getProviderGrids(provider, queryId) {
    const selectors = [];

    if (queryId) {
      selectors.push(`.jet-listing-grid[data-hw-query-id="${queryId}"]`);
      selectors.push(`.jet-listing-grid-${queryId}`);
      selectors.push(`.jet-listing-grid[data-query-id="${queryId}"]`);
      selectors.push(`.jet-listing-grid__items[data-hw-query-id="${queryId}"]`);
    }

    selectors.push('.jet-listing-grid:has([data-hw-skeleton="true"])');

    const $result = $(selectors.join(", ")).filter(".jet-listing-grid");

    return $result.length
      ? $result
      : $('[data-hw-skeleton="true"]').closest(".jet-listing-grid");
  }

  function captureGridLayoutState($grids) {
    if (!$grids || !$grids.length) {
      return;
    }

    $grids.each(function () {
      const $grid = $(this);
      $grid.find(".skeleton-loading").each(function () {
        const el = this;
        const dataset = el.dataset || {};

        if (dataset.hwSkeletonCaptured === "true") {
          return;
        }

        const style = window.getComputedStyle(el);
        const rect = el.getBoundingClientRect();

        SKELETON_STYLE_PROPS.forEach(
          ({
            style: prop,
            dataKey,
            inlineKey,
            skipValues = [],
            useRect = false,
          }) => {
            dataset[inlineKey] = el.style[prop] || "";

            let value = style[prop];

            if (useRect && (!value || value === "auto" || value === "0px")) {
              value = (prop === "width" ? rect.width : rect.height) + "px";
            }

            if (skipValues.includes(value)) {
              value = "";
            }

            if (value) {
              dataset[dataKey] = value;
            }
          }
        );

        dataset.hwSkeletonCaptured = "true";
      });
    });
  }

  function applyGridLayoutState($grids) {
    if (!$grids || !$grids.length) {
      return;
    }

    $grids.each(function () {
      const $grid = $(this);
      $grid.find(".skeleton-loading").each(function () {
        const el = this;
        const dataset = el.dataset || {};

        if (dataset.hwSkeletonCaptured !== "true") {
          return;
        }

        SKELETON_STYLE_PROPS.forEach(({ style: prop, dataKey, inlineKey }) => {
          const value = dataset[dataKey];
          if (value) {
            el.style[prop] = value;
          }
        });
      });
    });
  }

  function restoreGridLayoutState($grids) {
    if (!$grids || !$grids.length) {
      return;
    }

    $grids.each(function () {
      const $grid = $(this);
      $grid.find(".skeleton-loading").each(function () {
        const el = this;
        const dataset = el.dataset || {};

        if (dataset.hwSkeletonCaptured !== "true") {
          return;
        }

        SKELETON_STYLE_PROPS.forEach(({ style: prop, dataKey, inlineKey }) => {
          if (dataset.hasOwnProperty(dataKey)) {
            const originalInline = dataset[inlineKey];

            if (originalInline) {
              el.style[prop] = originalInline;
            } else {
              el.style[prop] = "";
            }

            delete dataset[dataKey];
            delete dataset[inlineKey];
          }
        });

        delete dataset.hwSkeletonCaptured;
      });
    });
  }

  function handleGridSkeletonState($grid) {
    if (!$grid || !$grid.length) {
      return;
    }

    const isLoading =
      $grid.hasClass("jet-listing-grid-loading") ||
      $grid.hasClass("hw-js-skeleton-loading");

    if (isLoading) {
      captureGridLayoutState($grid);
      applyGridLayoutState($grid);
    } else {
      restoreGridLayoutState($grid);
    }
  }

  function forceDevOn($widget) {
    try {
      if (!$widget || !$widget.length) {
        return;
      }
      $widget.addClass("hw-skeleton-dev");
      const $container = $widget.find(".elementor-widget-container").first()
        .length
        ? $widget.find(".elementor-widget-container").first()
        : $widget;
      const $overlay = $container.find(".hw-skeleton-overlay");
      if ($overlay.length) {
        $overlay.css("opacity", "1");
      }
      const $grid = $widget.closest(".jet-listing-grid");
      captureGridLayoutState($grid);
      applyGridLayoutState($grid);
    } catch (e) {}
  }

  function forceDevOff($widget) {
    try {
      if (!$widget || !$widget.length) {
        return;
      }
      $widget.removeClass("hw-skeleton-dev");
      const $container = $widget.find(".elementor-widget-container").first()
        .length
        ? $widget.find(".elementor-widget-container").first()
        : $widget;
      const $overlay = $container.find(".hw-skeleton-overlay");
      if ($overlay.length) {
        $overlay.css("opacity", "");
      }
      restoreGridLayoutState($widget.closest(".jet-listing-grid"));
    } catch (e) {}
  }

  // ===== JetEngine Load More/Infinite patch  — START =====
  // const patchJetEngineAjax = () => {
  //   try {
  //     if (
  //       !window.JetEngine ||
  //       typeof window.JetEngine.ajaxGetListing !== 'function' ||
  //       window.JetEngine.ajaxGetListing.__hwSkeletonPatched
  //     ) {
  //       return false;
  //     }

  //     const original = window.JetEngine.ajaxGetListing;

  //     window.JetEngine.ajaxGetListing = function(options, done, fail) {
  //       try {
  //         if (options && options.container) {
  //           const $container = options.container.jquery ? options.container : jQuery(options.container);
  //           const $grid = $container.closest('.jet-listing-grid.hw-jet-listing-skeleton');

  //           if ($grid.length) {
  //
  //             captureGridLayoutState($grid);
  //             applyGridLayoutState($grid);
  //           }
  //         }
  //       } catch (e) {}

  //       return original.apply(this, arguments);
  //     };

  //     window.JetEngine.ajaxGetListing.__hwSkeletonPatched = true;
  //     return true;
  //   } catch (e) {
  //     return false;
  //   }
  // };
  // ===== JetEngine Load More/Infinite patch — END =====

  const init = () => {
    scheduleUpdate();
    bindEvents();
    // patchJetEngineAjax();

    // Retry patch if JetEngine loads later
    if (
      !(
        window.JetEngine &&
        window.JetEngine.ajaxGetListing &&
        window.JetEngine.ajaxGetListing.__hwSkeletonPatched
      )
    ) {
      // const retry = () => {
      //   if (patchJetEngineAjax()) {
      //     clearInterval(retryTimer);
      //   }
      // };
      // const retryTimer = setInterval(retry, 500);
      // setTimeout(() => clearInterval(retryTimer), 5000);
    }

    window.HWSkeletonDev = window.HWSkeletonDev || {};
    window.HWSkeletonDev.enable = () => {
      HW_SKELETON_DEV = true;
      const $grids = jQuery(".jet-listing-grid.hw-jet-listing-skeleton");
      captureGridLayoutState($grids);
      $grids.addClass("hw-js-skeleton-loading");
      applyGridLayoutState($grids);
      collectTargets(document).each(function () {
        forceDevOn(jQuery(this));
      });
    };
    window.HWSkeletonDev.disable = () => {
      HW_SKELETON_DEV = false;
      const $grids = jQuery(".jet-listing-grid.hw-jet-listing-skeleton");
      $grids.removeClass("hw-js-skeleton-loading");
      restoreGridLayoutState($grids);
      collectTargets(document).each(function () {
        forceDevOff(jQuery(this));
      });
    };
    window.HWSkeletonDev.toggle = () => {
      if (HW_SKELETON_DEV) {
        window.HWSkeletonDev.disable();
      } else {
        window.HWSkeletonDev.enable();
      }
    };
    window.HWSkeletonDev.isEnabled = () => HW_SKELETON_DEV;
  };

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})(jQuery);
