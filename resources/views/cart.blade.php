@extends('layouts.master')
@section('content')
    <div
        class="balance-not-enough hidden fixed z-50 w-3/5 h-3/4 bg-gray-50 -translate-x-1/2 left-1/2 shadow-lg overflow-hidden rounded-lg">
        <div id="close-btn-balancenotenough" class="close group absolute top-7 right-6">
            <svg class="group-hover:fill-red-500" xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                <path
                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
            </svg>
        </div>
        <div class="wrappers p-6">
            <img src="{{ asset('images/static/connexpay.png') }}" alt="" class="h-8">
            <h1 class="text-2xl mt-4 font-semibold">Your Balance Is Not Enough For Doing This Transaction!</h1>
        </div>
    </div>


    <div class="container-cart flex h-full lg:flex-row flex-col gap-4 mb-24">
        @if (count($carts))
            <div class="cart-list w-full lg:w-4/5 p-0 lg:p-2">
                <div class="title px-3 mt-4">
                    <h1 class="text-lg font-semibold">Keranjangmu</h1>
                </div>
                <div class="choose-all-product-wrapper w-full lg:block hidden">
                    <div class="choose-all-product flex justify-start pl-2 mt-4">
                        <div class="checkbox-input flex items-center mt-1 gap-2 justify-center relative">
                            <input
                                class="checkbox-checkout-all h-5 w-5 peer shrink-0 relative checked:fill-white rounded-md border-2 border-[#303fe2] focus:outline-none checked:bg-[#303fe2] disabled:border-[1.5px] disabled:border-gray-300 appearance-none"
                                type="checkbox" name="checkproducts" id="checkproductall-dekstop">
                            <span
                                class="material-symbols-rounded pointer-events-none absolute left-0 text-[20px] text-white invisible peer-checked:visible">
                                done
                            </span>
                            <p class="text-slate-600">Pilih Semua Produk</p>
                        </div>
                    </div>
                </div>
                @foreach ($carts as $cart)
                    @if ($cart->product->deleted_at != '')
                        <div class="card my-3 bg-gray-200 relative p-4 rounded-lg flex gap-3 items-center">
                            <div class="product-img h-16 w-28 rounded-lg overflow-hidden">
                                <img class="w-full h-full object-cover" src="{{ asset('images/static/martgroup.png') }}"
                                    alt="mart-group">
                            </div>
                            <div class="description">
                                <p class="line-through">{{ $cart->product->name }}</p>
                                <p class="font-semibold price-products line-through"
                                    data-price="{{ $cart->product->price }}">
                                    {{ format_to_rp($cart->product->price) }}</p>
                                <span class="text-red-600">Product Not Available</span>
                            </div>
                            <div class="action absolute right-4 bottom-4">
                                <div class="flex items-center gap-2">
                                    <div id={{ $cart->id }} class="btn-delete-product group">
                                        <svg class="group-hover:fill-red-500" xmlns="http://www.w3.org/2000/svg"
                                            width="20" height="20" fill="currentColor" class="bi bi-trash3"
                                            viewBox="0 0 16 16">
                                            <path
                                                d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z" />
                                        </svg>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @else
                        <div
                            class="card my-3 @if (!$loop->last) border-b-[1.2px] border-gray-200 @endif relative p-4 shadow-sm bg-white">
                            <div class="detailandcheckbox flex items-start gap-3">
                                <div class="checkbox-input flex items-center mt-1 justify-center relative">
                                    <input data-quantity="{{ $cart->quantity }}" data-price="{{ $cart->product->price }}"
                                        id="{{ $cart->id }}"
                                        class="checkbox-checkout h-5 w-5 peer shrink-0 relative checked:fill-white rounded-md border-2 border-[#303fe2] focus:outline-none checked:bg-[#303fe2] disabled:border-[1.5px] disabled:border-gray-300 appearance-none"
                                        type="checkbox" name="checkproducts" id="checkproduct">
                                    <span
                                        class="material-symbols-rounded pointer-events-none absolute left-0 text-[20px] text-white invisible peer-checked:visible">
                                        done
                                    </span>
                                </div>
                                <div class="product-detail flex items-start gap-3">
                                    <div class="product-images">
                                        <div class="img h-16 w-16 rounded-lg overflow-hidden">
                                            @if (!empty($cart->product->photo) || File::exists(public_path($cart->product->photo)))
                                                <img src="{{ asset('images/default/mart.png') }}" alt=""
                                                    class="w-full h-full object-cover">
                                            @else
                                                <img src="{{ asset($cart->product->photo) }}" alt=""
                                                    class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="description">
                                        <p class="text-base w-full line-clamp-3">{{ $cart->product->name }}</p>
                                        <p class="font-semibold price-products" data-price="{{ $cart->product->price }}">
                                            {{ format_to_rp($cart->product->price) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="product-action flex items-center h-full justify-end gap-3">
                                <div class="add-wishlist mt-2">
                                    <span class="material-symbols-rounded text-zinc-500">
                                        favorite
                                    </span>
                                </div>
                                <div id="{{ $cart->id }}" class="delete-product mt-2 cursor-pointer">
                                    <span class="material-symbols-rounded text-zinc-500">
                                        delete
                                    </span>
                                </div>
                                <div class="qty-editor h-full">
                                    <div class="quantity">
                                        <div data-transid="{{ $cart->id }}"
                                            class="input-quantity flex bg-[#303fe2]/5 shadow-sm w-fit px-2 py-1 rounded-md">
                                            <button id="{{ $cart->product->id }}" class="cart-decrease">
                                                -
                                            </button>
                                            <input type="number"
                                                class="cart-input-quantity w-12 text-center bg-transparent focus:outline-none px-1"
                                                min="1" value="{{ $cart->quantity }}"
                                                max="{{ $cart->product->stock }}" data-selected="0"
                                                data-mbcurrentstock="{{ $cart->product->stock }}"
                                                data-singleprice="{{ $cart->product->price }}">
                                            <button class="cart-increase" class="text-[#303fe2]">
                                                +
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="price-list-balance w-2/5 mt-6 mb-8 lg:block hidden">
                <div class="price-list bg-white sticky top-28 rounded-xl w-full p-6 shadow-md">
                    <p class="text-lg font-semibold">Detail pesanan</p>
                    <div class="shopping-data flex items-center mt-4 text-gray-500 justify-between">
                        <p class="text-sm">Total Harga (<span class="product-qty-info">{{ $product_count }}</span> Produk)
                        </p>
                        <p data-prices="0" class="product-price-info">{{ format_to_rp($total_prices) }}</p>
                    </div>
                    <hr class="mt-2">
                    <div class="shoping-total font-semibold text-lg mt-4 flex items-center justify-between">
                        <p>Total</p>
                        <p><span class="product-price-info">{{ format_to_rp($total_prices) }}</span></p>
                    </div>
                    <button disabled
                        class="btn-checkout disabled-items w-full text-sm mt-5 text-white py-3  col-span-3 px-4 h-full font-semibold rounded-3xl gap-1 flex items-center justify-center">
                        Checkout (<span class="product-qty-info">0</span>)
                    </button>
                </div>
            </div>
        @else
            <div class="empty-cart flex justify-center w-full items-center">
                <div class="flex flex-col text-center pt-32">
                    <img src="{{ asset('images/static/Empty-Cart.svg') }}" alt="empty" class="h-48">
                    <h1 class="mt-8 font-bold text-2xl">Your Cart Is Empty</h1>
                </div>
            </div>
        @endif
    </div>
@endsection
