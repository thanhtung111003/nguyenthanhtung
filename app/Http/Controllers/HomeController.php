<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Mail;
use Session;
use App\Http\Requests;
use Illuminate\Support\Facades\Redirect; //thành công hoặc thất bại thì trả về trang gì đó
session_start();
class HomeController extends Controller
{
    public function send_mail() {
        //send mail
        $to_name = "Thanh Tung";
        $to_email = "thanhtungnguyen02113@gmail.com"; //send to this mail

        $data = array("name" => "Mail từ tài khoản khách hàng", "body" => 'Mail gửi về vấn đề hàng hóa'); //body of mail.blade.php

        Mail::send('pages.send_mail', $data, function($message) use ($to_name, $to_email){
            $message->to($to_email)->subject('Test thử gửi mail google'); //send this mail with subject
            $message->from($to_email, $to_name); //send from this mail
        });
        //return Redirect('/')->with('message', '');
        //--send mail
    }
    public function index(Request $request) {
        //Seo
        $meta_desc = "Chuyên bán điện thoại, laptop, tivi, phụ kiện chính hãng";
        $meta_keywords = "dien thoai, điện thoại, laptop, phu kien chinh hang, phụ kiện chính hãng";
        $meta_title = "T-Shop Điện thoại, laptop, phụ kiện chính hãng";
        $url_canonical = $request->url();
        //--Seo

        $cate_product = DB::table('tbl_category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();
        // $all_product = DB::table('tbl_product')
        // ->join('tbl_category_product', 'tbl_category_product.category_id', '=', 'tbl_product.category_id')
        // ->join('tbl_brand', 'tbl_brand.brand_id', '=', 'tbl_product.brand_id')
        // ->orderBy('tbl_product.product_id', 'desc')->get();
        $all_product = DB::table('tbl_product')->where('product_status', '0')->orderBy('product_id', 'desc')->get();

        return view('pages.home')->with('category', $cate_product)
                                 ->with('brand', $brand_product)
                                 ->with('all_product', $all_product)
                                 ->with('meta_desc', $meta_desc)
                                 ->with('meta_keywords', $meta_keywords)
                                 ->with('meta_title', $meta_title)
                                 ->with('url_canonical', $url_canonical);
    }
    public function search(Request $request){
        //Seo
        $meta_desc = "Tìm kiếm sản phẩm";
        $meta_keywords = "Tìm kiếm sản phẩm";
        $meta_title = "Tìm kiếm sản phẩm";
        $url_canonical = $request->url();
        //--Seo

        $keywords=$request->keywords_submit;
        $cate_product = DB::table('tbl_category_product')->where('category_status', '0')->orderBy('category_id', 'desc')->get();
        $brand_product = DB::table('tbl_brand')->where('brand_status', '0')->orderBy('brand_id', 'desc')->get();
        // $all_product = DB::table('tbl_product')
        // ->join('tbl_category_product', 'tbl_category_product.category_id', '=', 'tbl_product.category_id')
        // ->join('tbl_brand', 'tbl_brand.brand_id', '=', 'tbl_product.brand_id')
        // ->orderBy('tbl_product.product_id', 'desc')->get();
        $search_product = DB::table('tbl_product')->where('product_name', 'like', '%'.$keywords.'%')->get();

        return view('pages.sanpham.search')->with('category', $cate_product)
                                           ->with('brand', $brand_product)
                                           ->with('search_product', $search_product)
                                           ->with('meta_desc', $meta_desc)
                                           ->with('meta_keywords', $meta_keywords)
                                           ->with('meta_title', $meta_title)
                                           ->with('url_canonical', $url_canonical);

    }
}
