<?php
/**
 * WC Order HPOS query editor component template.
 */
?>
<div class="jet-engine-edit-page__fields">
	<div class="cx-vui-collapse__heading">
		<h3 class="cx-vui-subtitle"><?php esc_html_e('WC Order HPOS Query', 'hw-ele-woo-dynamic'); ?></h3>
	</div>
	<div class="cx-vui-panel">
		<cx-vui-tabs
			:in-panel="false"
			value="general"
			layout="vertical"
		>
			<cx-vui-tabs-panel
				name="general"
				:label="isInUseMark( generalProps ) + '<?php esc_html_e('General', 'hw-ele-woo-dynamic'); ?>'"
				key="general"
			>
				<cx-vui-input
					label="<?php esc_html_e('Custom Context', 'hw-ele-woo-dynamic'); ?>"
					description="<?php esc_html_e('Leave empty for all orders or pass a user ID, or %current_user_id%.', 'hw-ele-woo-dynamic'); ?>"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_customer"
					v-model="query.customer"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.customer"></jet-query-dynamic-args>
				</cx-vui-input>

				<cx-vui-f-select
					label="<?php esc_html_e('Statuses', 'hw-ele-woo-dynamic'); ?>"
					description="<?php esc_html_e('Choose order statuses to include. Leave empty to use all statuses.', 'hw-ele-woo-dynamic'); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:multiple="true"
					:options-list="statusOptions"
					size="fullwidth"
					name="query_statuses"
					v-model="query.statuses"
				></cx-vui-f-select>

				<cx-vui-f-select
					label="<?php esc_html_e('Payment Methods', 'hw-ele-woo-dynamic'); ?>"
					description="<?php esc_html_e('Limit orders to specific payment gateways. Leave empty for all.', 'hw-ele-woo-dynamic'); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					:multiple="true"
					:options-list="paymentMethodOptions"
					size="fullwidth"
					name="query_payment_methods"
					v-model="query.payment_methods"
				></cx-vui-f-select>
			</cx-vui-tabs-panel>

			<cx-vui-tabs-panel
				name="date"
				:label="isInUseMark( dateProps ) + '<?php esc_html_e('Date', 'hw-ele-woo-dynamic'); ?>'"
				key="date"
			>
				<cx-vui-input
					label="<?php esc_html_e('Date After', 'hw-ele-woo-dynamic'); ?>"
					description="<?php esc_html_e('Return orders created on or after this date. Accepts YYYY-MM-DD or macro values.', 'hw-ele-woo-dynamic'); ?>"
					type="text"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_date_after"
					v-model="query.date_after"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.date_after"></jet-query-dynamic-args>
				</cx-vui-input>

				<cx-vui-input
					label="<?php esc_html_e('Date Before', 'hw-ele-woo-dynamic'); ?>"
					description="<?php esc_html_e('Return orders created on or before this date. Accepts YYYY-MM-DD or macro values.', 'hw-ele-woo-dynamic'); ?>"
					type="text"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_date_before"
					v-model="query.date_before"
					>
						<jet-query-dynamic-args v-model="dynamicQuery.date_before"></jet-query-dynamic-args>
					</cx-vui-input>
			</cx-vui-tabs-panel>

			<cx-vui-tabs-panel
				name="pagination"
				:label="isInUseMark( paginationProps ) + '<?php esc_html_e('Pagination', 'hw-ele-woo-dynamic'); ?>'"
				key="pagination"
			>
				<cx-vui-switcher
					label="<?php esc_html_e('Enable Pagination', 'hw-ele-woo-dynamic'); ?>"
					description="<?php esc_html_e('Toggle to load orders in pages instead of a single batch.', 'hw-ele-woo-dynamic'); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					name="query_paginate"
					v-model="query.paginate"
				></cx-vui-switcher>

				<cx-vui-input
					label="<?php esc_html_e('Posts Per Page', 'hw-ele-woo-dynamic'); ?>"
					description="<?php esc_html_e('Number of post to show per page. Use `-1` to show all posts (the `Offset` parameter is ignored with a -1 value)', 'hw-ele-woo-dynamic'); ?>"
					type="number"
					step="1"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					name="query_per_page"
					v-model="query.per_page"
				></cx-vui-input>

				<cx-vui-input
					label="<?php esc_html_e('Offset', 'hw-ele-woo-dynamic'); ?>"
					description="<?php esc_html_e('Number of post to displace or pass over. Warning: Setting the offset parameter overrides/ignores the paged parameter and breaks pagination', 'hw-ele-woo-dynamic'); ?>"
					type="number"
					min="0"
					step="1"
					:wrapper-css="[ 'equalwidth', 'has-macros' ]"
					size="fullwidth"
					name="query_offset"
					v-model="query.offset"
				>
					<jet-query-dynamic-args v-model="dynamicQuery.offset"></jet-query-dynamic-args>
				</cx-vui-input>
			</cx-vui-tabs-panel>
		</cx-vui-tabs>
	</div>
</div>
