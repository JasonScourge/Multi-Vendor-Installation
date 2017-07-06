<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

use Tygh\Enum\VendorPayoutTypes;
use Tygh\Registry;
use Tygh\Models\VendorPlan;
use Tygh\Models\Company;
use Tygh\VendorPayouts;

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_vendor_plans_install()
{
    // vendor_payouts table. These fields shouldn't remove: They are used by vendor_commission
    $fields = fn_get_table_fields('vendor_payouts');
    if (!in_array('commission_amount', $fields)) {
        db_query("ALTER TABLE ?:vendor_payouts ADD `commission_amount` decimal(12,2) NOT NULL default '0'");
    }
    if (!in_array('commission', $fields)) {
        db_query("ALTER TABLE ?:vendor_payouts ADD `commission` decimal(12,2) NOT NULL default '0'");
    }
    if (!in_array('commission_type', $fields)) {
        db_query("ALTER TABLE ?:vendor_payouts ADD `commission_type` char(1) NOT NULL default 'A'");
    }

    // import data exported from the vendor commission add-on
    $vendors_demo = Registry::get('config.dir.addons') . 'vendor_plans/database/demo_vendors.sql';
    if (file_exists($vendors_demo)) {
        db_import_sql_file($vendors_demo, 16348, false, false);
        fn_rm($vendors_demo);
    }

    db_query("REPLACE INTO ?:privileges (privilege, is_default, section_id) VALUES ('view_vendor_plans', 'Y', 'vendors')");
    db_query("REPLACE INTO ?:privileges (privilege, is_default, section_id) VALUES ('manage_vendor_plans', 'Y', 'vendors')");
}

function fn_vendor_plans_uninstall()
{
    db_query("DELETE FROM ?:privileges WHERE privilege IN (?a)", array('view_vendor_plans', 'manage_vendor_plans'));
}

function fn_vendor_plans_get_companies(&$params, &$fields, &$sortings, &$condition, &$join, &$auth, &$lang_code, &$group)
{
    $fields[] = '?:vendor_plan_descriptions.plan';
    $sortings['plan'] = '?:vendor_plan_descriptions.plan';
    $join .= db_quote(
        ' LEFT JOIN ?:vendor_plan_descriptions'
        . ' ON ?:companies.plan_id = ?:vendor_plan_descriptions.plan_id'
        . ' AND ?:vendor_plan_descriptions.lang_code = ?s',
        $lang_code
    );
    if (!empty($params['plan_id'])) {
        $condition .= db_quote(' AND ?:companies.plan_id IN (?n)', (array)$params['plan_id']);
    }
}

function fn_vendor_plans_get_company_data(&$company_id, &$lang_code, &$extra, &$fields, &$join, &$condition)
{
    $fields[] = '?:vendor_plan_descriptions.plan';
    $join .= db_quote(
        ' LEFT JOIN ?:vendor_plan_descriptions'
        . ' ON companies.plan_id = ?:vendor_plan_descriptions.plan_id'
        . ' AND ?:vendor_plan_descriptions.lang_code = ?s',
        $lang_code
    );
}

function fn_vendor_plans_update_company_pre(&$company_data, &$company_id, &$lang_code, &$can_update)
{
    // Getting current plan
    $company_data['current_plan'] = 0;
    if ($company_id) {
        $curent_data = db_get_row("SELECT plan_id, status FROM ?:companies WHERE company_id = ?i", $company_id);
        $company_data['current_plan'] = $curent_data['plan_id'];
        if (empty($company_data['status'])) {
            $company_data['status'] = $curent_data['status'];
        }
        if (empty($company_data['plan_id'])) {
            $company_data['plan_id'] = $company_data['current_plan'];
        }
    }

    // Check plan availability
    if (!empty($company_data['plan_id'])) {
        $selected_plan = VendorPlan::model()->find($company_data['plan_id'], array(
            'allowed_for_company_id' => $company_id
        ));
        if (!$selected_plan) {
            $company_data['plan_id'] = $company_data['current_plan'] ?: 0;
        }
    }

    // Set default plan
    if (empty($company_data['plan_id']) && empty($company_data['current_plan'])) {
        $default_plan = VendorPlan::model()->find(array(
            'is_default' => 1,
            'allowed_for_company_id' => $company_id,
        ));
        if ($default_plan) {
            $company_data['plan_id'] = $default_plan->plan_id;
        }
    }

    // Check params availability
    if (
        Registry::get('runtime.company_id')
        && !empty($company_data['plan_id'])
        && $company_data['plan_id'] != $company_data['current_plan']
    ) {
        $plan = VendorPlan::model()->find($company_data['plan_id'], array(
            'allowed_for_company_id' => $company_id,
            'check_availability' => true,
        ));
        if (!empty($plan->avail_errors) || Registry::ifGet('addons.vendor_plans.allow_vendors_to_change_plan', 'N') == 'N') {
            fn_set_notification('E', __('error'), __('vendor_plans.plan_not_available_text'));
            $can_update = false;
        }
    }
}

function fn_vendor_plans_update_company(&$company_data, &$company_id, &$lang_code, &$action)
{
    if (
        isset($company_data['plan_id'])
        && isset($company_data['current_plan'])
        && $company_data['plan_id'] != $company_data['current_plan']
        && $company_data['status'] != 'N'
    ) {
        $company = Company::model()->find($company_id);
        /** @var \Tygh\Mailer\Mailer $mailer */
        $mailer = Tygh::$app['mailer'];

        $mailer->send(array(
            'to' => 'company_support_department',
            'from' => 'default_company_support_department',
            'data' => array(
                'vendor' => $company,
                'plan' => $company->plan,
            ),
            'template_code' => 'vendor_plans_plan_changed',
            'tpl' => 'addons/vendor_plans/companies/plan_changed.tpl',
            'company_id' => $company->company_id,
        ), 'A', $company->lang_code);

        if ($company_data['status'] == 'A') {
            $company->payment();
        }
    }
}

function fn_vendor_plans_change_company_status_before_mail(&$company_id, &$status_to, &$reason, &$status_from, &$skip_query, &$notify, &$company_data, &$user_data)
{
    $company = Company::model()->find($company_id);
    $user_data['plan'] = $company->plan; // Need for email notifications
    if ($status_from != 'A' && $status_to == 'A') {
        $company->payment();
    }
}

function fn_vendor_plans_delete_category_after(&$category_id)
{
    db_query("UPDATE ?:vendor_plans SET categories = ?p", fn_remove_from_set('categories', $category_id));
}

/**
 * Hook handler: adds commission values based on the order totals when the order is placed.
 *
 * @param array  $order_info   Order infromation from ::fn_get_order_info()
 * @param array  $company_data Company data the order belongs to
 * @param string $action       Performed action: '' when editing the order, 'save' when saving
 * @param string $order_status Order status
 * @param array  $cart         Cart contents and user information necessary for purchase
 * @param array  $data         Payout data to be stored in the DB
 * @param int    $payout_id    Payout ID
 * @param array  $auth         User authentication data (e.g. uid, usergroup_ids, etc.)
 */
function fn_vendor_plans_mve_place_order(&$order_info, &$company_data, &$action, &$order_status, &$cart, &$data, &$payout_id, &$auth)
{
    $data = fn_calculate_commission_for_payout($order_info, $company_data, $data);
}

/**
 * Hook handler: adds commission values based on the difference between the order totals when the order is changed.
 *
 * @param array $new_order_info New order information from ::fn_get_order_info()
 * @param array $order_id       Order ID
 * @param array $old_order_info Old order information from ::fn_get_order_info()
 * @param array $company_data   Company data the order belongs to
 * @param int   $payout_id      Existing payout ID
 * @param array $payout_data    Payout data to be stored in the DB
 */
function fn_vendor_plans_mve_update_order(&$new_order_info, &$order_id, &$old_order_info, &$company_data, &$payout_id, &$payout_data)
{
    if (Registry::get('addons.vendor_plans.include_shipping') == 'N') {
        $payout_data['order_amount'] =
            ($new_order_info['total'] - $new_order_info['shipping_cost']) -
            ($old_order_info['total'] - $old_order_info['shipping_cost']);
    }

    if (Registry::get('addons.vendor_plans.include_payment_surcharge') == 'Y') {
        $payout_data['order_amount'] +=
            $old_order_info['payment_surcharge'] -
            $new_order_info['payment_surcharge'];
    }

    $payout_data = fn_calculate_commission_for_payout($new_order_info, $company_data, $payout_data);
}

function fn_vendor_plans_mve_place_order_post(&$order_id, &$action, &$order_status, &$cart, &$auth, &$order_info, &$company_data, &$data, &$payout_id)
{
    if ($order_info['is_parent_order'] != 'Y' && !empty($order_info['company_id'])) {
        if ($company = Company::model()->find($order_info['company_id'])) {
            $company->canGetRevenue(true);
        }
    }
}

/**
 * Hook handler: excludes the commission amount from a transaction value for a vendor
 * and sets a transaction value to the commission amount for an admin.
 *
 * @param VendorPayouts $instance       VendorPayouts instance
 * @param array         $params         Search parameters
 * @param int           $items_per_page Items per page
 * @param array         $fields         SQL query fields
 * @param string        $join           JOIN statement
 * @param string        $condition      General condition to filter payouts
 * @param string        $date_condition Additional condition to filter payouts by date
 * @param string        $sorting        ORDER BY statemet
 * @param string        $limit          LIMIT statement
 */
function fn_vendor_plans_vendor_payouts_get_list(&$instance, &$params, &$items_per_page, &$fields, &$join, &$condition, &$date_condition, &$sorting, &$limit)
{
    if ($instance->getVendor()) {
        $fields['payout_amount'] = 'IF(payouts.order_id <> 0, payouts.order_amount - payouts.commission_amount, payouts.payout_amount)';
    } else {
        $fields['payout_amount'] = 'IF(payouts.order_id <> 0, payouts.commission_amount, payouts.payout_amount)';
    }
}

/**
 * Hook handler: excludes commission from vendor income and sets admin income as a sum of commissions.
 *
 * @param VendorPayouts $instance       VendorPayouts instance
 * @param array         $params         Search parameters
 * @param array         $fields         SQL query fields
 * @param string        $join           JOIN statement
 * @param string        $condition      General condition to filter payouts
 * @param string        $date_condition Additional condition to filter payouts by date
 */
function fn_vendor_plans_vendor_payouts_get_income(&$instance, &$params, &$fields, &$join, &$condition, &$date_condition)
{
    if ($instance->getVendor()) {
        $fields['orders_summary'] = 'SUM(payouts.order_amount) - SUM(payouts.commission_amount)';
    } else {
        $fields['orders_summary'] = 'SUM(payouts.commission_amount)';
    }
}

function fn_vendor_plans_get_categories(&$params, &$join, &$condition, &$fields, &$group_by, &$sortings, &$lang_code)
{
    if (Registry::get('runtime.company_id')) {
        $company_id = Registry::get('runtime.company_id');
    } elseif (!empty($params['company_ids'])) {
        $company_id = (int) $params['company_ids'];
    }
    if (!empty($company_id)) {
        $plan = VendorPlan::model()->find(array('company_id' => $company_id));
        if ($plan && $plan->category_ids) {
            $company_condition = db_quote(' AND ?:categories.category_id IN (?n)', $plan->category_ids);
            Registry::set('runtime.vendor_plans_company_condition', $company_condition);
            $condition .= $company_condition;
        }
    }
}

function fn_vendor_plans_get_categories_after_sql(&$categories, &$params, &$join, &$condition, &$fields, &$group_by, &$sortings, &$sorting, &$limit, &$lang_code)
{
    // we can't build the correct tree for vendors if there are not available parent categories
    if ($company_condition = Registry::get('runtime.vendor_plans_company_condition')) {
        Registry::del('runtime.vendor_plans_company_condition');
        $selected_ids = array_keys($categories);
        // so get skipped parent categories ids
        $parent_ids = array();
        foreach ($categories as $v) {
            if ($v['parent_id'] && !in_array($v['parent_id'], $selected_ids)) {
                $parent_ids = array_merge($parent_ids, explode('/', $v['id_path']));
            }
        }
        if ($parent_ids) {
            $_condition = str_replace($company_condition, '', $condition);
            $_condition .= db_quote(' AND ?:categories.category_id IN (?a)', array_unique($parent_ids));
            $fields[] = '1 as disabled'; //mark such categories as disabled
            $parent_categories = db_get_hash_array(
                "SELECT " . implode(',', $fields)
                . " FROM ?:categories"
                . " LEFT JOIN ?:category_descriptions"
                . "  ON ?:categories.category_id = ?:category_descriptions.category_id"
                . "  AND ?:category_descriptions.lang_code = ?s $join"
                . " WHERE 1 ?p $group_by $sorting ?p",
                'category_id', $lang_code, $_condition, $limit
            );
            $categories = $categories + $parent_categories;
        }
    }
}

function fn_vendor_plans_get_category_data(&$category_id, &$field_list, &$join, &$lang_code, &$conditions)
{
    if ($company_id = Registry::get('runtime.company_id')) {
        $plan = VendorPlan::model()->find(array('company_id' => $company_id));
        if ($plan && $plan->category_ids) {
            $conditions .= db_quote(" AND ?:categories.category_id IN(?n)", $plan->category_ids);
        }
    }
}

function fn_vendor_plans_take_payment_surcharge(&$products, &$take_surcharge_from_vendor)
{
    if (Registry::get('addons.vendor_plans.include_payment_surcharge') == 'Y') {
        $take_surcharge_from_vendor = true;
    }
}

function fn_vendor_plans_set_admin_notification(&$user_data)
{
    Tygh::$app['session']['vendor_plans_payments'] = true;
}

function fn_vendor_plans_dispatch_before_display()
{
    if (!empty(Tygh::$app['session']['vendor_plans_payments'])) {
        unset(Tygh::$app['session']['vendor_plans_payments']);
        Tygh::$app['view']->assign('vendor_plans_payments', true);
    }
}

function fn_vendor_plans_update_product_pre(&$product_data, &$product_id, &$lang_code, &$can_update)
{
    if ($can_update) {

        $company_id = Registry::get('runtime.company_id');
        if (!$company_id) {
            if (isset($product_data['company_id'])) {
                $company_id = $product_data['company_id'];
            } else {
                $company_id = db_get_field('SELECT company_id FROM ?:products WHERE product_id = ?i', $product_id);
            }
        }

        if ($company_id) {

            $company = Company::model()->find($company_id);
            if (!$product_id && !$company->canAddProduct(true)) {
                $can_update = false;
            }

            if ($company->category_ids) {
                if (
                    !empty($product_data['main_category'])
                    && !in_array($product_data['main_category'], $company->category_ids)
                ) {
                    unset($product_data['main_category']);
                }
                if (empty($product_data['category_ids'])) {
                    $product_data['category_ids'] = db_get_fields(
                        "SELECT category_id FROM ?:products_categories WHERE product_id = ?i", $product_id
                    );
                }
                $product_data['category_ids'] = array_intersect($product_data['category_ids'], $company->category_ids);
                if (empty($product_data['category_ids'])) {
                    $can_update = false;
                }
                if (!$can_update) {
                    fn_set_notification('E', __('error'), __('vendor_plans.category_is_empty'));
                }
            }

        } else {
            $can_update = false;
        }

    }
}

// Exim

function fn_vendor_plans_import_check_object_id($primary_object_id, &$processed_data, &$skip_record)
{
    $company = Company::current();
    if ($company && empty($primary_object_id) && !$company->canAddProduct(true)) {
        $skip_record = true;
        $processed_data['S'] ++;
    }
}

/**
 * Calculates commission based on payout.
 *
 * @param array $order_info   Order information
 * @param array $company_data Company to which order belongs to
 * @param array $payout_data  Payout data to be written to database
 *
 * @return array Payout data with calculated commission
 */
function fn_calculate_commission_for_payout($order_info, $company_data, $payout_data)
{
    if (
        $payout_data
        && $company_data['plan_id']
        && ($plan = VendorPlan::model()->find($company_data['plan_id']))
    ) {
        $commission = $order_info['total'] > 0 ? $plan->commission : 0;
        $total = $order_info['total'];
        $shipping_cost = 0;
        $surcharge = 0;

        if ($payout_data['payout_type'] == VendorPayoutTypes::ORDER_CHANGED
            || $payout_data['payout_type'] == VendorPayoutTypes::ORDER_REFUNDED
        ) {
            // Commission is calculated as the difference between orders
            $total = $payout_data['order_amount'];
        } else {
            //Calculate commission amount and check if we need to include shipping cost
            if (Registry::get('addons.vendor_plans.include_shipping') == 'N') {
                $shipping_cost = $order_info['shipping_cost'];
            }

            //Check if we need to take payment surcharge from vendor
            if (Registry::get('addons.vendor_plans.include_payment_surcharge') == 'Y') {
                $surcharge = $order_info['payment_surcharge'];
            }
        }

        $commission_amount = ($total - $shipping_cost) * $commission / 100;
        $commission_amount += $surcharge;

        if (abs($commission_amount) > abs($total)) {
            $commission_amount = $total;
        }

        $payout_data['commission'] = $commission;
        $payout_data['commission_amount'] = $commission_amount;
        $payout_data['commission_type'] = 'P'; // Backward compatibility
    }

    return $payout_data;
}

/**
 * Hook handler: adds commission values to refunds performed via RMA add-on.
 *
 * @param array $data        Request parameters
 * @param array $order_info  Order information from ::fn_get_orders()
 * @param array $return_info Return request from ::fn_get_return_info()
 * @param array $payout_data Payout data to be stored in the DB
 */
function fn_vendor_plans_rma_update_details_create_payout(&$data, &$order_info, &$return_info, &$payout_data)
{
    $company_data = fn_get_company_data($order_info['company_id']);

    $payout_data = fn_calculate_commission_for_payout($order_info, $company_data, $payout_data);
}

/**
 * Hook handler: adds commission values to refunds performed via PayPal add-on.
 *
 * @param int   $order_id    Order ID
 * @param array $data        IPN request parameters
 * @param array $order_info  Order info from ::fn_get_order_info()
 * @param array $payout_data Payout data to be stored in the DB
 */
function fn_vendor_plans_process_paypal_ipn_create_payout(&$order_id, &$data, &$order_info, &$payout_data)
{
    $company_data = fn_get_company_data($order_info['company_id']);

    $payout_data = fn_calculate_commission_for_payout($order_info, $company_data, $payout_data);
}