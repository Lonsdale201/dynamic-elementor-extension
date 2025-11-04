(function( $ ) {
	'use strict';

	Vue.component( 'jet-wc-order-hpos-query', {
		template: '#jet-wc-order-hpos-query',
		mixins: [
			window.JetQueryWatcherMixin,
			window.JetQueryTabInUseMixin,
		],
		props: [ 'value', 'dynamicValue' ],
		data: function() {
			const localized = window.jet_query_component_wc_order_hpos || {};

			return {
				query: {},
				dynamicQuery: {},
				statuses: localized.statuses || [],
				paymentMethods: localized.payment_methods || [],
				defaultPerPage: localized.default_per_page || 10,
				defaultOffset: 0,
			};
		},
		created: function() {
			this.query = { ...this.value };
			this.dynamicQuery = { ...this.dynamicValue };

			if ( typeof this.query.customer === 'undefined' ) {
				this.$set( this.query, 'customer', '' );
			}

			if ( ! Array.isArray( this.query.statuses ) ) {
				this.$set( this.query, 'statuses', [] );
			}

			if ( ! Array.isArray( this.query.payment_methods ) ) {
				this.$set( this.query, 'payment_methods', [] );
			}

			if ( typeof this.query.paginate === 'undefined' ) {
				this.$set( this.query, 'paginate', true );
			}

			if ( typeof this.query.include_products === 'undefined' ) {
				this.$set( this.query, 'include_products', '' );
			}

			if ( typeof this.query.exclude_products === 'undefined' ) {
				this.$set( this.query, 'exclude_products', '' );
			}

			if ( typeof this.query.per_page === 'undefined' || ! this.query.per_page ) {
				this.$set( this.query, 'per_page', this.defaultPerPage );
			}

			if ( typeof this.dynamicQuery.customer === 'undefined' ) {
				this.$set( this.dynamicQuery, 'customer', '' );
			}

			if ( typeof this.query.date_after === 'undefined' ) {
				this.$set( this.query, 'date_after', '' );
			}

			if ( typeof this.query.date_before === 'undefined' ) {
				this.$set( this.query, 'date_before', '' );
			}

			if ( typeof this.dynamicQuery.date_after === 'undefined' ) {
				this.$set( this.dynamicQuery, 'date_after', '' );
			}

			if ( typeof this.dynamicQuery.date_before === 'undefined' ) {
				this.$set( this.dynamicQuery, 'date_before', '' );
			}

			if ( typeof this.query.offset === 'undefined' ) {
				this.$set( this.query, 'offset', this.defaultOffset );
			}

			if ( typeof this.dynamicQuery.offset === 'undefined' ) {
				this.$set( this.dynamicQuery, 'offset', '' );
			}
		},
		computed: {
			statusOptions: function() {
				return this.statuses;
			},
			paymentMethodOptions: function() {
				return this.paymentMethods;
			},
			generalProps: function() {
			return [ 'customer', 'statuses', 'payment_methods' ];
		},
		includeExcludeProps: function() {
			return [ 'include_products', 'exclude_products' ];
		},
		paginationProps: function() {
			return [ 'paginate', 'per_page', 'offset' ];
		},
			dateProps: function() {
				return [ 'date_after', 'date_before' ];
			}
		},
		watch: {
			'query.paginate': function( enabled ) {
				if ( enabled && ( ! this.query.per_page || parseInt( this.query.per_page, 10 ) === -1 ) ) {
					this.$set( this.query, 'per_page', this.defaultPerPage );
				}

				if ( enabled && parseInt( this.query.offset, 10 ) > 0 ) {
					this.resetOffset();
				}

				if ( enabled && this.hasDynamicValue( this.dynamicQuery.offset ) ) {
					this.resetOffset();
				}
			},
			'query.per_page': function( value ) {
				const parsed = parseInt( value, 10 );

				if ( -1 === parsed ) {
					this.$set( this.query, 'paginate', false );
					this.resetOffset();
				}
			},
			'query.offset': function( value ) {
				const parsed = parseInt( value, 10 );

				if ( parsed > 0 ) {
					this.$set( this.query, 'paginate', false );
				}
			},
			'dynamicQuery.offset': function( value ) {
				if ( this.hasDynamicValue( value ) ) {
					this.$set( this.query, 'paginate', false );
				}
			}
		},
		methods: {
			resetOffset: function() {
				this.$set( this.query, 'offset', this.defaultOffset );
				this.$set( this.dynamicQuery, 'offset', '' );
			},
			hasDynamicValue: function( value ) {
				if ( 'undefined' === typeof value || null === value ) {
					return false;
				}

				if ( Array.isArray( value ) ) {
					return value.some( function( item ) {
						return item && item.toString().trim().length;
					} );
				}

				if ( 'object' === typeof value ) {
					return Object.keys( value ).some( function( key ) {
						return value[ key ] && value[ key ].toString().trim().length;
					} );
				}

				return value.toString().trim().length > 0;
			}
		}
	} );

})( jQuery );
