<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function addProductToCart(Request $request,  $product_id)
    {
        try {
            $product = Product::find($product_id);
            if (!$product) {
                return $this->errorResponse('Product Not Found', 404);
            }
            $cart = Cart::where('patient_id', Auth::id())->first();
            if (!$cart) {
                $cart = Cart::create([
                    'patient_id' => Auth::id(),
                    'total_price' => 0
                ]);
            }
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += 1;
                $cartItem->price = $cartItem->quantity * $product->price;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'price' => $product->price,
                ]);
            }
            $cart->total_price = $cart->cartItems->sum('price');
            $cart->save();

            return $this->successResponse(
                $cart,
               'Product added to cart successfully',
                201);
        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
    public function updateCartItemQuantity(Request $request, Product $product)
    {
        try {
            $quantity = $request->input('quantity');
            $cartItem = CartItem::where('product_id', $product->id)->first();
            if (!$cartItem) {
                return $this->errorResponse('Cart item not found', 404);
            }
            if ($quantity <= 0) {
                return $this->errorResponse('Quantity must be greater than zero', 400);
            }
            // Update the cart item quantity and price
            $cartItem->quantity += $quantity;
            $cartItem->price = $cartItem->quantity * $cartItem->product->price;
            $cartItem->save();

            // Update the cart's total price
            $cart = $cartItem->cart;
            $cart->total_price = $cart->cartItems->sum('price');
            $cart->save();

            return $this->successResponse(
                $cart,
           'Cart item updated successfully',
                200);
        } catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }

    public  function showProductOnCart(){
        try {
            $cart = Cart::where('patient_id',Auth::id())->first();
            if (!$cart){
                return $this->errorResponse('Patient cart not found', 404);
            }
            $patientCart = $cart->cartItems()->with('product')->get();
            return $this->successResponse(
                $patientCart,
                'Cart Items Retrieved Successfully',
                200
            );
        }catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }

    public  function deleteProductFromCart(Request $request,$product_id){
        try {
            $product = CartItem::where('product_id',$product_id)->first();
            if (!$product){
                return $this->errorResponse('Product not found in cart', 404);
            }
            $product->delete();
            return $this->successResponse(null,'Product deleted from cart successfully',200);
        }catch (\Exception $exception) {
            return $this->errorResponse(['message' => $exception->getMessage()], 500);
        }
    }
}
