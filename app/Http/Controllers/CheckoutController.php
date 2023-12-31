<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Cart;
use Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect; //thành công hoặc thất bại thì trả về trang gì đó
session_start();

class CheckoutController extends Controller
{
    public function AuthLogin(){
        $admin_id = Session::get('admin_id');
        if($admin_id){
            return Redirect::to('dashboard');
        }
        else{
            return Redirect::to('admin')->send();
        }
    }
    public function view_order($orderId){
        $this->AuthLogin();
        $order_by_id = DB::table('tbl_order')
        ->join('tbl_customer', 'tbl_order.customer_id', '=', 'tbl_customer.customer_id')
        ->join('tbl_shipping', 'tbl_order.shipping_id', '=', 'tbl_shipping.shipping_id')
        ->join('tbl_order_details', 'tbl_order.order_id', '=', 'tbl_order_details.order_id')
        ->select('tbl_order.*', 'tbl_customer.*','tbl_shipping.*','tbl_order_details.*')->first();

        $manage_order_by_id = view('admin.view_order')->with('order_by_id', $order_by_id);
        return view('admin_layout')->with('admin.view_order', $manage_order_by_id);

    }
    public function login_checkout(Request $request) {
         //Seo
         $meta_desc = "Đăng nhập thanh toán";
         $meta_keywords = "Đăng nhập thanh toán";
         $meta_title = "Đăng nhập thanh toán";
         $url_canonical = $request->url();
         //--Seo

        $cate_product = DB::table('tbl_category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();

        return view('pages.checkout.login_checkout')->with('category', $cate_product)
                                                    ->with('brand', $brand_product)
                                                    ->with('meta_desc', $meta_desc)
                                                    ->with('meta_keywords', $meta_keywords)
                                                    ->with('meta_title', $meta_title)
                                                    ->with('url_canonical', $url_canonical);
    }
    public function add_customer(Request $request) {

        $data = array();
        $data['customer_name'] = $request->customer_name;
        $data['customer_phone'] = $request->customer_phone;
        $data['customer_email'] = $request->customer_email;
        $data['customer_password'] = md5($request->customer_password);

        $customer_id = DB::table('tbl_customer')->insertGetId($data);

        Session::put('customer_id', $customer_id);
        Session::put('customer_name', $request->customer_name);

        return Redirect::to('/checkout');

    } 
    public function checkout(Request $request){
        //Seo
        $meta_desc = "Đăng nhập thanh toán";
        $meta_keywords = "Đăng nhập thanh toán";
        $meta_title = "Đăng nhập thanh toán";
        $url_canonical = $request->url();
        //--Seo
        $cate_product = DB::table('tbl_category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();

        return view('pages.checkout.show_checkout')->with('category', $cate_product)
                                                   ->with('brand', $brand_product)
                                                   ->with('meta_desc', $meta_desc)
                                                   ->with('meta_keywords', $meta_keywords)
                                                   ->with('meta_title', $meta_title)
                                                   ->with('url_canonical', $url_canonical);
    }
    public function save_checkout_customer(Request $request){
        $data = array();
        $data['shipping_name'] = $request->shipping_name;
        $data['shipping_phone'] = $request->shipping_phone;
        $data['shipping_email'] = $request->shipping_email;
        $data['shipping_notes'] = $request->shipping_notes;
        $data['shipping_address'] = $request->shipping_address;

        $shipping_id = DB::table('tbl_shipping')->insertGetId($data);

        Session::put('shipping_id', $shipping_id);

        return Redirect::to('/payment');

    }
    public function payment(Request $request){
        //Seo
        $meta_desc = "Thanh toán";
        $meta_keywords = "Thanh toán";
        $meta_title = "Thanh toán";
        $url_canonical = $request->url();
        //--Seo

        $cate_product = DB::table('tbl_category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();

        return view('pages.checkout.payment')->with('category', $cate_product)
                                             ->with('brand', $brand_product)
                                             ->with('meta_desc', $meta_desc)
                                             ->with('meta_keywords', $meta_keywords)
                                             ->with('meta_title', $meta_title)
                                             ->with('url_canonical', $url_canonical);
    }
    public function order_place(Request $request){
        //insert payment_method
        $data = array();
        $data['payment_method'] = $request->payment_option;
        $data['payment_status'] = 'Đang chờ xử lí';
        $payment_id = DB::table('tbl_payment')->insertGetId($data);

        //insert order
        $order_data = array();
        $order_data['customer_id'] = Session::get('customer_id');
        $order_data['shipping_id'] = Session::get('shipping_id');
        $order_data['payment_id'] = $payment_id;
        $order_data['order_total'] = Cart::total();
        $order_data['order_status'] = 'Đang chờ xử lí';
        $order_id = DB::table('tbl_order')->insertGetId($order_data);

        //insert order_details
        $content = Cart::content();
        foreach($content as $v_content){
            $order_d_data = array();
            $order_d_data['order_id'] = $order_id;
            $order_d_data['product_id'] = $v_content->id;
            $order_d_data['product_name'] = $v_content->name;
            $order_d_data['product_price'] = $v_content->price;
            $order_d_data['product_sales_quantity'] = $v_content->qty;
            DB::table('tbl_order_details')->insert($order_d_data);
        }
        //Seo
        $meta_desc = "Đặt hàng";
        $meta_keywords = "Đặt hàng";
        $meta_title = "Đặt hàng";
        $url_canonical = $request->url();
        //--Seo
        if($data['payment_method']==1){
            echo "Thanh toán thẻ ATM";
        }elseif($data['payment_method']==2){
            Cart::destroy();
            $cate_product = DB::table('tbl_category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
            $brand_product = DB::table('tbl_brand')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();
    
            return view('pages.checkout.handcash')->with('category', $cate_product)
                                                  ->with('brand', $brand_product)
                                                  ->with('meta_desc', $meta_desc)
                                                  ->with('meta_keywords', $meta_keywords)
                                                  ->with('meta_title', $meta_title)
                                                  ->with('url_canonical', $url_canonical);
        }else{
            echo "Ghi sổ";
        }
    }
    public function logout_checkout(){
        Session::flush();
        return Redirect::to('/login-checkout');
    }
    public function login_customer(Request $request){
        $email=$request->email_account;
        $password=md5($request->password_account);
        $result=DB::table('tbl_customer')->where('customer_email', $email)->where('customer_password', $password)->first();

        if($result){
            Session::put('customer_id',$result->customer_id);
            return Redirect::to('/checkout');
        }else{
            return Redirect::to('login-checkout');
        }
    }
    public function manage_order(){
        $this->AuthLogin();
        $all_order = DB::table('tbl_order')
        ->join('tbl_customer', 'tbl_order.customer_id', '=', 'tbl_customer.customer_id')
        ->select('tbl_order.*', 'tbl_customer.customer_name')
        ->orderBy('tbl_order.order_id', 'desc')->get();
        $manage_order = view('admin.manage_order')->with('all_order', $all_order);
        return view('admin_layout')->with('admin.manage_order', $manage_order);
    }
}
