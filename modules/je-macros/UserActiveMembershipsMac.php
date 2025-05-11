<?php

namespace HelloWP\HWEleWooDynamic\Modules\JEMacros;

use Jet_Engine_Base_Macros;

class UserActiveMembershipsMac extends Jet_Engine_Base_Macros {

    public function macros_tag() {
        return 'wc_current_user_active_memberships';
    }

    public function macros_name() {
        return 'WC Current User Active Memberships';
    }

    public function macros_args() {
        return array(
            'membership_status' => array(
                'label'   => 'Membership Status',
                'type'    => 'select',
                'options' => $this->get_membership_statuses(),
                'default' => 'any',
                'multiple' => false, 
                'description' => 'Select membership statuses to include, or Any for all statuses.',
            )
        );
    }

    public function macros_callback( $args = array() ) {
        $user_id = get_current_user_id();
        $selected_status = $args['membership_status'] ?? 'any';

        if (!$user_id) {
            return 'User is not logged in';
        }

        $membership_ids = $this->get_user_memberships_by_status($user_id, $selected_status);

        if (empty($membership_ids)) {
            return 'User has no memberships with the selected statuses';
        }

        return implode(', ', $membership_ids);
    }

    protected function get_membership_statuses() {
        return [
            'any' => 'Any',
            'active' => 'Active',
            'paused' => 'Paused',
            'expired' => 'Expired',
            'cancelled' => 'Cancelled'
        ];
    }

    protected function get_user_memberships_by_status($user_id, $status) {
        if (!function_exists('wc_memberships_get_user_memberships')) {
            return [];
        }

        $user_memberships = wc_memberships_get_user_memberships($user_id);
        $filtered_memberships = [];

        if ($status === 'any') {
            foreach ($user_memberships as $membership) {
                $filtered_memberships[] = $membership->get_id();
            }
        } else {
            foreach ($user_memberships as $membership) {
                if ($membership->get_status() === $status) {
                    $filtered_memberships[] = $membership->get_id();
                }
            }
        }

        return $filtered_memberships;
    }
}
