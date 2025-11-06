(function ($) {
  "use strict";

  const TARGET_SELECTOR =
    '.skeleton-multitext-loading.elementor-widget-text-editor, .skeleton-multitext-loading[data-widget_type="jet-listing-dynamic-field.default"], .skeleton-multitext-loading.elementor-widget-jet-listing-dynamic-field';
  const OVERLAY_CLASS = "hw-skeleton-overlay";
  const LINE_CLASS = "hw-skeleton-overlay__line";
  const RESIZE_DEBOUNCE = 150;
  const GRID_OBSERVER_KEY = "__hwSkeletonGridObserver";
  const AUTO_APPLY_ATTRIBUTE = "data-hw-skeleton-auto";
  const AUTO_APPLY_MARK = "data-hw-skeleton-auto-applied";
  const AUTO_SKIP_VALUES = ["false", "0", "no", "off", "skip"];
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
      style: "minHeight",
      dataKey: "hwSkeletonOriginalMinheight",
      inlineKey: "hwSkeletonInlineMinheight",
      skipValues: ["", "auto", "0px"],
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

    $container.addClass("skeleton-keep");

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
      class: `${OVERLAY_CLASS} skeleton-keep`,
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
        class: `${OVERLAY_CLASS} skeleton-keep`,
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

      // Ensure intermediate wrappers between the widget and the nearest skeleton container stay visible.
      // Elementor often inserts extra divs (e.g., e-con-inner) without skeleton classes; mark them as keepers.
      $widget.parentsUntil(".skeleton-loading").addClass("skeleton-keep");

      captureGridLayoutState($widget.closest(".jet-listing-grid"));
      observeParentGrid($widget);
      buildOverlay($widget);
      handleGridSkeletonState($widget.closest(".jet-listing-grid"));
    });
  };

  const scheduleUpdate = (root = document) => {
    requestAnimationFrame(() => {
      autoApplySkeletons(root);
      updateTargets(root);
      applyShapes(root);
      applyDynamicGallery(root);
      applyRadiusVars(root);
    });
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

  // ===========================================
  // JS adapter: registry -> shape classes
  // Adds generic shape classes to targeted inner elements.
  // Keeps CSS small and avoids widget-specific duplication.
  // ===========================================

  const HW_SKEL_REGISTRY = {
    "icon-box.default": {
      class: "skeleton-icon-box-loading",
      targets: {
        ".elementor-icon-box-icon .elementor-icon": "circle",
        ".elementor-icon-box-title:not(:empty)": "block",
        ".elementor-icon-box-description:not(:empty)": "block",
      },
    },
    "icon.default": {
      class: "skeleton-icon-loading",
      targets: {
        ".elementor-icon": "circle",
      },
    },
    "icon-list.default": {
      class: "skeleton-list-loading",
      targets: {
        ".elementor-icon-list-item:not(:empty)": "list",
      },
    },
    "image-box.default": {
      class: "skeleton-image-box-loading",
      targets: {
        ".elementor-image-box-img": "block",
        ".elementor-image-box-title:not(:empty)": "block",
        ".elementor-image-box-description:not(:empty)": "block",
      },
    },
  };

  const HW_AUTO_SKEL_REGISTRY = {
    "text-editor.default": {
      classes: ["skeleton-loading", "skeleton-multitext-loading"],
    },
    "heading.default": {
      classes: ["skeleton-loading"],
      classTargets: {
        "skeleton-loading": ".elementor-heading-title",
      },
    },
    "jet-listing-dynamic-field.default": {
      classes: ["skeleton-loading", "skeleton-multitext-loading"],
      onApply($widget) {
        try {
          const $slider = $widget.find(".jet-engine-gallery-slider");
          const $grid = $widget.find(".jet-engine-gallery-grid");

          if ($slider.length) {
            $widget.removeClass("skeleton-multitext-loading skeleton-loading");
            $widget.addClass("skeleton-dynamice-sl-loading");

            $slider
              .find(".jet-engine-gallery-slider__item-wrap")
              .each(function () {
                const $item = jQuery(this);
                if ($item.data("hwSkelGalleryApplied") === "1") return;
                ensureSkeletonItemHeight($item);
                $item.addClass("skeleton-loading");
                $item.data("hwSkelGalleryApplied", "1");
              });
            return;
          }

          if ($grid.length) {
            $widget.removeClass("skeleton-multitext-loading skeleton-loading");
            $widget.addClass("skeleton-dynamice-g-loading");

            $grid.find(".jet-engine-gallery-grid__item-wrap").each(function () {
              const $item = jQuery(this);
              if ($item.data("hwSkelGalleryApplied") === "1") return;
              ensureSkeletonItemHeight($item);
              $item.addClass("skeleton-loading");
              $item.data("hwSkelGalleryApplied", "1");
            });
          }
        } catch (e) {}
      },
    },
    "icon-box.default": {
      classes: ["skeleton-icon-box-loading", "skeleton-silent-bg"],
      classTargets: {
        "skeleton-silent-bg": ".elementor-icon-box-wrapper",
      },
    },
    "icon-list.default": {
      classes: ["skeleton-list-loading"],
    },
    "image.default": {
      classes: ["skeleton-loading", "skeleton-z-bg"],
      selector:
        '.elementor-widget-image[data-widget_type="image.default"] .elementor-image, .elementor-widget-image[data-widget_type="image.default"] img, .elementor-widget-image[data-widget_type="image.default"] picture',
      onApply($widget) {
        try {
          // Ensure the outer widget acts as the skeleton target if we matched a child
          const $owner = $widget.closest(
            '.elementor-widget-image[data-widget_type="image.default"]'
          );
          if ($owner.length) {
            $owner.addClass("skeleton-loading");
          }
        } catch (e) {}
      },
    },
    "jet-listing-dynamic-image.default": {
      classes: ["skeleton-loading", "skeleton-z-bg"],
      selector:
        '.elementor-widget-jet-listing-dynamic-image[data-widget_type="jet-listing-dynamic-image.default"] .jet-listing-dynamic-image__wrap, .elementor-widget-jet-listing-dynamic-image[data-widget_type="jet-listing-dynamic-image.default"] img, .elementor-widget-jet-listing-dynamic-image[data-widget_type="jet-listing-dynamic-image.default"] picture',
      onApply($widget) {
        try {
          const $owner = $widget.closest(
            '.elementor-widget-jet-listing-dynamic-image[data-widget_type="jet-listing-dynamic-image.default"]'
          );
          if ($owner.length) {
            $owner.addClass("skeleton-loading");
          }
        } catch (e) {}
      },
    },
    "dynamic-opening-hours.default": {
      classes: ["skeleton-loading"],
    },
    "jet-listing-dynamic-terms.default": {
      classes: ["skeleton-loading"],
    },
    "jet-listing-dynamic-link.default": {
      classes: ["skeleton-loading"],
    },
    "jet-engine-data-store-button.default": {
      classes: ["skeleton-loading"],
    },
    "button.default": {
      classes: ["skeleton-loading"],
    },
    "icon.default": {
      classes: ["skeleton-icon-loading"],
      selector: '[data-widget_type="icon.default"]',
    },
    "video.default": {
      classes: ["skeleton-loading", "skeleton-z-bg"],
    },
    "jet-video.default": {
      classes: ["skeleton-loading"],
    },
    "jet-button.default": {
      classes: ["skeleton-loading"],
    },
    "jet-headline.default": {
      classes: ["skeleton-loading"],
    },
    "shortcode.default": {
      classes: ["skeleton-loading"],
    },
    "image-box.default": {
      classes: ["skeleton-image-box-loading", "skeleton-silent-bg"],
      classTargets: {
        "skeleton-silent-bg": ".elementor-image-box-wrapper",
      },
    },
  };

  function isAutoApplyOptOut(element) {
    try {
      if (!element || typeof element.getAttribute !== "function") {
        return false;
      }

      const value = (
        element.getAttribute(AUTO_APPLY_ATTRIBUTE) || ""
      ).toLowerCase();

      if (!value || value === "yes") {
        return false;
      }

      return AUTO_SKIP_VALUES.indexOf(value) !== -1;
    } catch (e) {
      return false;
    }
  }

  function markAutoApplied($widget, classes = []) {
    try {
      if (!$widget || !$widget.length) {
        return;
      }

      const existing = ($widget.attr(AUTO_APPLY_MARK) || "")
        .split(/\s+/)
        .filter(Boolean);
      const merged = Array.from(
        new Set([...existing, ...classes.filter(Boolean)])
      );

      if (merged.length) {
        $widget.attr(AUTO_APPLY_MARK, merged.join(" "));
      } else if (!$widget.attr(AUTO_APPLY_MARK)) {
        $widget.attr(AUTO_APPLY_MARK, "1");
      }
    } catch (e) {}
  }

  function ensureSkeletonItemHeight($item) {
    try {
      if (!$item || !$item.length) {
        return;
      }

      const el = $item[0];
      if (!el || typeof el.getBoundingClientRect !== "function") {
        return;
      }

      const dataset = el.dataset || {};
      if (!dataset.hwSkeletonInlineMinheight) {
        dataset.hwSkeletonInlineMinheight = el.style.minHeight || "";
      }

      const isSlider = $item
        .closest(".skeleton-dynamice-sl-loading")
        .length > 0;

      if (dataset.hwSkeletonOriginalMinheight) {
        el.style.minHeight = dataset.hwSkeletonOriginalMinheight;
        el.dataset = dataset;
        return;
      }

      if (isSlider) {
        dataset.hwSkeletonOriginalMinheight = "100%";
        el.style.minHeight = "100%";
        el.dataset = dataset;
        return;
      }

      let height = Math.round($item.outerHeight());
      if (!height || height <= 0) {
        const rect = el.getBoundingClientRect();
        if (rect && rect.height > 0) {
          height = Math.round(rect.height);
        }
      }

      if (height && height > 0) {
        dataset.hwSkeletonOriginalMinheight = height + "px";
        el.style.minHeight = dataset.hwSkeletonOriginalMinheight;
      }

      el.dataset = dataset;
    } catch (e) {}
  }

  function autoApplySkeletons(root = document) {
    try {
      const containers = new Set();
      const $root = jQuery(root || document);

      if ($root.length) {
        $root.each(function () {
          const node = this;

          if (node === document) {
            document
              .querySelectorAll(`[${AUTO_APPLY_ATTRIBUTE}="yes"]`)
              .forEach((el) => containers.add(el));
            return;
          }

          if (!node || node.nodeType !== 1) {
            return;
          }

          if (
            typeof node.getAttribute === "function" &&
            node.getAttribute(AUTO_APPLY_ATTRIBUTE) === "yes"
          ) {
            containers.add(node);
          }

          if (node.querySelectorAll) {
            node
              .querySelectorAll(`[${AUTO_APPLY_ATTRIBUTE}="yes"]`)
              .forEach((el) => containers.add(el));
          }

          if (node.closest) {
            const parent = node.closest(`[${AUTO_APPLY_ATTRIBUTE}="yes"]`);
            if (parent) {
              containers.add(parent);
            }
          }
        });
      }

      if (!containers.size) {
        document
          .querySelectorAll(`[${AUTO_APPLY_ATTRIBUTE}="yes"]`)
          .forEach((el) => containers.add(el));
      }

      if (!containers.size) {
        return;
      }

      containers.forEach((container) => {
        const $container = jQuery(container);

        Object.entries(HW_AUTO_SKEL_REGISTRY).forEach(
          ([widgetType, config]) => {
            const selector =
              config.selector || `[data-widget_type="${widgetType}"]`;
            const $widgets = $container.find(selector);

            if (!$widgets.length) {
              return;
            }

            $widgets.each(function () {
              if (isAutoApplyOptOut(this)) {
                return;
              }

              const $widget = jQuery(this);
              const classes = Array.isArray(config.classes)
                ? config.classes
                : [];
              const classTargets = config.classTargets || {};
              const applied = [];

              classes.forEach((cls) => {
                if (!cls) {
                  return;
                }

                const targetSpec = Object.prototype.hasOwnProperty.call(
                  classTargets,
                  cls
                )
                  ? classTargets[cls]
                  : null;

                const applyTo = [];

                const pushTargets = ($targets) => {
                  if ($targets && $targets.length) {
                    $targets.each(function () {
                      applyTo.push(jQuery(this));
                    });
                  }
                };

                if (
                  !targetSpec ||
                  targetSpec === "self" ||
                  targetSpec === "widget"
                ) {
                  pushTargets($widget);
                } else if (targetSpec === false || targetSpec === "none") {
                  // skip applying this class
                  return;
                } else {
                  const selectors = Array.isArray(targetSpec)
                    ? targetSpec
                    : [targetSpec];
                  selectors.forEach((sel) => {
                    if (typeof sel === "string" && sel.trim().length) {
                      pushTargets($widget.find(sel));
                    }
                  });
                }

                if (!applyTo.length) {
                  pushTargets($widget);
                }

                applyTo.forEach(($target) => {
                  if (!$target.hasClass(cls)) {
                    $target.addClass(cls);
                    applied.push(cls);
                  }
                });
              });

              if (typeof config.onApply === "function") {
                try {
                  config.onApply($widget, $container);
                } catch (e) {}
              }

              if (classes.length || applied.length) {
                markAutoApplied($widget, classes.length ? classes : applied);
              }
            });
          }
        );
      });
    } catch (e) {}
  }

  // Manual or post-auto handler for dynamic gallery skeleton
  function applyDynamicGallery(root = document) {
    try {
      const $root = jQuery(root || document);
      const configs = [
        {
          widgetClass: "skeleton-dynamice-g-loading",
          container: ".jet-engine-gallery-grid",
          items: ".jet-engine-gallery-grid__item-wrap",
        },
        {
          widgetClass: "skeleton-dynamice-sl-loading",
          container: ".jet-engine-gallery-slider",
          items: ".jet-engine-gallery-slider__item-wrap",
        },
      ];

      configs.forEach(({ widgetClass, container, items }) => {
        const selector =
          '[data-widget_type="jet-listing-dynamic-field.default"].' +
          widgetClass;
        const $widgets = $root
          .find(selector)
          .add($root.is(selector) ? $root : []);

        if (!$widgets.length) {
          return;
        }

        $widgets.each(function () {
          const $widget = jQuery(this);
          const $container = $widget.find(container);
          if (!$container.length) {
            return;
          }

          $container.find(items).each(function () {
            const $item = jQuery(this);
            if ($item.data("hwSkelGalleryApplied") === "1") {
              return;
            }
            ensureSkeletonItemHeight($item);
            $item.addClass("skeleton-loading");
            $item.data("hwSkelGalleryApplied", "1");
          });
        });
      });
    } catch (e) {}
  }

  const SHAPES_CLASS = {
    block: "hw-skel-block",
    circle: "hw-skel-circle",
    list: "hw-skel-list-item",
  };

  function readCircleRadius(el) {
    try {
      const cs = window.getComputedStyle(el);
      if (!cs) {
        return null;
      }

      const radius = cs.borderRadius;
      if (!radius) {
        return null;
      }

      return radius.trim();
    } catch (e) {
      return null;
    }
  }

  function getEffectiveCircleRadius(el) {
    const direct = readCircleRadius(el);
    if (direct) {
      return direct;
    }

    const innerIcon =
      el.querySelector &&
      el.querySelector(
        ".elementor-icon, .elementor-icon-wrap, .elementor-icon-box-icon-inner"
      );

    if (innerIcon) {
      const innerRadius = readCircleRadius(innerIcon);
      if (innerRadius) {
        return innerRadius;
      }
    }

    return null;
  }

  function applyCircleRadius($element, radius) {
    try {
      if (!$element || !$element.length || !radius) {
        return;
      }

      $element.css("--hw-skel-circle-radius", radius);
    } catch (e) {}
  }

  function applyShapeClasses($widget) {
    try {
      if (!$widget || !$widget.length) return;
      if ($widget.data("hwSkelShapesApplied") === "1") return;

      const type = $widget.attr("data-widget_type");
      const cfg = HW_SKEL_REGISTRY[type];
      if (!cfg) return;
      if (!$widget.hasClass(cfg.class)) return;

      Object.entries(cfg.targets).forEach(([sel, shape]) => {
        const cls = SHAPES_CLASS[shape];
        if (!cls) return;
        $widget.find(sel).each(function () {
          const $target = jQuery(this);
          let originalRadius = null;

          if (shape === "circle") {
            originalRadius = getEffectiveCircleRadius(this);
            if (originalRadius !== null) {
              applyCircleRadius($target, originalRadius);
            }
          }
          // Skip empty image wrapper for Image Box (no media, no bg)
          try {
            if (shape === "block" && $target.is(".elementor-image-box-img")) {
              const hasMedia =
                $target.find("img, picture, svg, video, source").length > 0;
              let hasBg = false;
              try {
                const cs = window.getComputedStyle(this);
                hasBg =
                  !!cs && cs.backgroundImage && cs.backgroundImage !== "none";
              } catch (e) {}
              if (!hasMedia && !hasBg) {
                return; // do not add shape
              }
            }
          } catch (e) {}

          $target.addClass(cls);

          if (shape === "circle" && originalRadius === null) {
            applyCircleRadius($target, "50%");
          }
        });
      });

      $widget.data("hwSkelShapesApplied", "1");
    } catch (e) {}
  }

  const SHAPED_SELECTOR = [
    '[data-widget_type="icon-box.default"].skeleton-icon-box-loading',
    '[data-widget_type="icon-list.default"].skeleton-list-loading',
    '[data-widget_type="image-box.default"].skeleton-image-box-loading',
    '[data-widget_type="icon.default"].skeleton-icon-loading',
  ].join(",");

  function applyShapes(root = document) {
    try {
      const $root = jQuery(root);
      $root.find(SHAPED_SELECTOR).each(function () {
        applyShapeClasses(jQuery(this));
      });
    } catch (e) {}
  }

  function applyRadiusVars(root = document) {
    try {
      const $root = jQuery(root);
      const $containers = $root
        .find('[data-hw-skeleton="true"]')
        .add($root.is('[data-hw-skeleton="true"]') ? $root : []);
      $containers.each(function () {
        const el = this;
        const radius = el.getAttribute("data-hw-skeleton-radius");
        if (radius !== null && radius !== undefined && radius !== "") {
          const v = Math.max(0, parseInt(radius, 10)) + "px";
          el.style.setProperty("--hw-skeleton-radius", v);
        }
      });
    } catch (e) {}
  }

  function getProviderGrids(provider, queryId) {
    let $result = $();

    if (queryId) {
      const selectors = [
        `.jet-listing-grid[data-hw-query-id="${queryId}"]`,
        `.jet-listing-grid-${queryId}`,
        `.jet-listing-grid[data-query-id="${queryId}"]`,
      ];

      $result = $(selectors.join(", ")).filter(".jet-listing-grid");

      if (!$result.length) {
        const $items = $(
          `.jet-listing-grid__items[data-hw-query-id="${queryId}"], .jet-listing-grid__items[data-query-id="${queryId}"]`
        );
        if ($items.length) {
          $result = $items.closest(".jet-listing-grid");
        }
      }
    }

    if (!$result.length && provider) {
      try {
        const candidate =
          provider.$provider ||
          provider.container ||
          provider.$container ||
          provider;

        const $candidate = candidate ? $(candidate) : $();

        if ($candidate.length) {
          if ($candidate.is(".jet-listing-grid")) {
            $result = $candidate;
          } else {
            $result = $candidate.closest(".jet-listing-grid");
          }
        }
      } catch (e) {}
    }

    return $result;
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

  const init = () => {
    scheduleUpdate();
    bindEvents();

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
