@extends('layout')
@section('content')
<!--features_items-->
                    <div class="features_items">
						<h2 class="title text-center">Tất cả sản phẩm</h2>
						@foreach($all_product as $key => $product)
							<div class="col-sm-4">
								<div class="product-image-wrapper">
									<div class="single-products">
											<div class="productinfo text-center">
												<form>
													<input type="hidden" name="cart_product_id" value="" class="">
														<img src="{{URL::to('public/uploads/product/'.$product->product_image)}}" alt="" />
														<h2>{{number_format($product->product_price).' '.'VND'}}</h2>
														<p>{{$product->product_name}}</p>
														<!-- <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Thêm giỏ hàng</a> -->
														<button type="button" class="btn btn-default add-to-cart" name="add-to-cart">Thêm giỏ hàng</button>
												</form>
											</div>
									</div>
									<div class="choose">
										<ul class="nav nav-pills nav-justified">
											<li><a href="#"><i class="fa fa-plus-square"></i>Yêu thích</a></li>
											<li><a href="#"><i class="fa fa-plus-square"></i>So sánh</a></li>
										</ul>
									</div>
								</div>
							</div>
						@endforeach

					</div>
<!--features_items--> 
@endsection