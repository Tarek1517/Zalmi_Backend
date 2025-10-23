<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use function App\Http\Helpers\showPrices;


class SettingController extends Controller
{
    public function getGlobalSetting()
    {
        //header Categories
        $categoryIds = json_decode(getSetting('home_categories'));
        $categories = [];
        if ($categoryIds !== null && count($categoryIds) > 0) {
            $categories = Category::whereIn('id', $categoryIds ?? [])
                ->with('children')
                ->get()
                ->sortBy(function ($categories) use ($categoryIds) {
                    return array_search($categories->id, $categoryIds);
                })
                ->values();
        }

        $categoryIds = json_decode(getSetting('header_categories'));
        $Headercategories = [];
        if ($categoryIds !== null && count($categoryIds) > 0) {
            $Headercategories = Category::whereIn('id', $categoryIds ?? [])
                ->with('children')
                ->get()
                ->sortBy(function ($Headercategories) use ($categoryIds) {
                    return array_search($Headercategories->id, $categoryIds);
                })
                ->values();
        }

        $productIds = json_decode(getSetting('home_products'));
        $products = [];
        if ($productIds !== null && count($productIds) > 0) {
            $products = Product::whereIn('id', $productIds ?? [])
                ->with(
                    'category',
                    'baseVariations.color',
                    'baseVariations.variationsImages',
                    'baseVariations.prices.size',
                )
                ->get()
                ->sortBy(function ($products) use ($productIds) {
                    return array_search($products->id, $productIds);
                })
                ->values();
        }



        // //footer columns
        // $footerColumns = Footer::query()->orderBy('order_number')->get();
        // foreach( $footerColumns as $column){
        //     $footerPageIds = json_decode($column->pages);
        //     $footerPages = Page::query()->whereIn('id', $footerPageIds)->select('slug', 'title')->get();
        //     $column['pages'] = $footerPages;
        // }

        $settings = [
            'currency' => getSetting('currency'),
            'currency_symbol' => getSetting('currency_symbol'),
            'home_categories' => $categories,
            'header_categories' => $Headercategories,
            'home_products' => $products,
            'all_categories' => Category::query()->select('slug', 'name', 'id', 'banner')->get(),
            'app_name' => getSetting('app_name'),
            'app_url' => getSetting('app_url'),
            'logo_light' => env('APP_URL') . getSetting('logo_light'),
            'logo_dark' => env('APP_URL') . getSetting('logo_dark'),
            'email' => getSetting('email'),
            'phone_number' => getSetting('phone_number'),
            'whatsapp_number' => getSetting('whatsapp_number'),
            'hotline_number' => getSetting('hotline_number'),
            'facebook_link' => getSetting('facebook_link'),
            'youtube_link' => getSetting('youtube_link'),
            'instagram_link' => getSetting('instagram_link'),
            'linkedin_link' => getSetting('linkedin_link'),
        ];
        return response()->json($settings);
    }
    public function getAllSetting()
    {
        $settings = [
            'home_category' => json_decode(getSetting('home_category')),
            'home_category_2' => json_decode(getSetting('home_category_2')),
            'header_categories' => json_decode(getSetting('header_categories')),
            'top_categories' => json_decode(getSetting('top_categories')),
            'home_products' => json_decode(getSetting('home_products')),
            'home_blogs' => json_decode(getSetting('home_blogs')),
            'currency' => getSetting('currency'),
            'currency_symbol' => getSetting('currency_symbol'),
            'app_name' => getSetting('app_name'),
            'app_url' => getSetting('app_url'),
            'logo_light' => getSetting('logo_light'),
            'logo_dark' => getSetting('logo_dark'),
            'email' => getSetting('email'),
            'product_return_refund_policy' => getSetting('product_return_refund_policy'),
            'phone_number' => getSetting('phone_number'),
            'whatsapp_number' => getSetting('whatsapp_number'),
            'hotline_number' => getSetting('hotline_number'),
            'facebook_link' => getSetting('facebook_link'),
            'youtube_link' => getSetting('youtube_link'),
            'instagram_link' => getSetting('instagram_link'),
            'linkedin_link' => getSetting('linkedin_link'),
        ];


        $settings['logo_light_url'] = $settings['logo_light']
            ? asset('storage/' . $settings['logo_light'])
            : null;

        $settings['logo_dark_url'] = $settings['logo_dark']
            ? asset('storage/' . $settings['logo_dark'])
            : null;

        return response()->json($settings);
    }

    public function saveHeaderSetting(Request $request)
    {
        $data = $request->all();
        $keysToTransform = [
            'home_categories',
            'home_products',
            'header_categories',
            'home_blogs',
        ];
        foreach ($data as $type => $value) {
            $settings = Setting::where('key', $type)->first();
            if ($settings) {
                if ($value !== null) {
                    if (is_array($value)) {
                        if (in_array($type, $keysToTransform)) {
                            $value = array_map('intval', $value);
                        }
                        $settings->value = json_encode($value);
                    } else {
                        $settings->value = $value;
                    }
                    $settings->save();
                }
            } else {
                if ($value !== null) {
                    $settings = new Setting();
                    $settings->key = $type;
                    if (is_array($value)) {
                        if (in_array($type, $keysToTransform)) {
                            $value = array_map('intval', $value);
                        }
                        $settings->value = json_encode($value);
                    } else {
                        $settings->value = $value;
                    }
                    $settings->save();
                }
            }
        }
        if ($request->hasFile('logo_light')) {
            $settings = Setting::where('key', 'logo_light')->first();
            if ($settings?->value) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $settings->value));
            }

            $path = $request->file('logo_light')->store('uploads', 'public');
            $settings->value = $path; // ✅ no '/storage/' prefix
            $settings->save();
        }

        if ($request->hasFile('logo_dark')) {
            $settings = Setting::where('key', 'logo_dark')->first();
            if ($settings?->value) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $settings->value));
            }

            $path = $request->file('logo_dark')->store('uploads', 'public');
            $settings->value = $path; // ✅ no '/storage/' prefix
            $settings->save();
        }


        return response()->json(['status' => 'success'], Response::HTTP_OK);
    }
}
