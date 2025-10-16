<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'title' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition_label' => '良好',
                'category' => 'ファッション',
            ],
            [
                'title' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition_label' => '目立った傷や汚れなし',
                'category' => '家電',
            ],
            [
                'title' => '玉ねぎ3束',
                'price' => 300,
                'brand' => 'なし',
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition_label' => 'やや傷や汚れあり',
                'category' => '生活雑貨',
            ],
            [
                'title' => '革靴',
                'price' => 4000,
                'brand' => null, // 「なし」= NULL
                'description' => 'クラシックなデザインの革靴',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition_label' => '状態が悪い',
                'category' => 'ファッション',
            ],
            [
                'title' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'description' => '高性能なノートパソコン',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition_label' => '良好',
                'category' => '家電',
            ],
            [
                'title' => 'マイク',
                'price' => 8000,
                'brand' => 'なし',
                'description' => '高音質のレコーディング用マイク',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition_label' => '目立った傷や汚れなし',
                'category' => '家電',
            ],
            [
                'title' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition_label' => 'やや傷や汚れあり',
                'category' => 'ファッション',
            ],
            [
                'title' => 'タンブラー',
                'price' => 500,
                'brand' => 'なし',
                'description' => '使いやすいタンブラー',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition_label' => '状態が悪い',
                'category' => '生活雑貨',
            ],
            [
                'title' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'description' => '手動のコーヒーミル',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition_label' => '良好',
                'category' => '家電',
            ],
            [
                'title' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'description' => '便利なメイクアップセット',
                'image' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition_label' => '目立った傷や汚れなし',
                'category' => '生活雑貨',
            ],
        ];

        foreach ($rows as $r) {
            $brand     = ($r['brand'] ?? null);
            $brand     = ($brand === 'なし') ? null : $brand;
            $imagePath = $this->downloadToPublic($r['image'] ?? null);

            Product::create([
                'title'       => $r['title'],
                'brand'       => $brand,
                'description' => $r['description'] ?? null,
                'price'       => (int)($r['price'] ?? 0),
                'condition'   => $this->mapCondition($r['condition_label'] ?? ''),
                'category'    => $r['category'] ?? null,
                'image_path'  => $imagePath,
            ]);
        }
    }

    private function mapCondition(string $label): int
    {
        $label = trim($label);

        return match (true) {
            str_contains($label, '新品')            => 1,
            str_contains($label, '未使用')          => 2,
            str_contains($label, '目立った')        => 3,
            str_contains($label, 'やや')            => 4,
            str_contains($label, '傷や汚れあり')     => 5,
            str_contains($label, '悪い')            => 6,
            str_contains($label, '良好')            => 3, // 「良好」は3（全体の中央？）
            default                                 => 3,
        };
    }

    private function downloadToPublic(?string $url): ?string
    {
        if (empty($url)) return null;

        try {
            $path   = parse_url($url, PHP_URL_PATH);
            $ext    = pathinfo($path ?? '', PATHINFO_EXTENSION) ?: 'jpg';
            $name   = 'products/' . Str::uuid() . '.' . $ext;

            $res = Http::retry(2, 500)->get($url);
            if ($res->successful()) {
                Storage::disk('public')->put($name, $res->body());
                return $name; 
            }
        } catch (\Throwable $e) {
            // 失敗時は画像なしで続行
        }
        return null;
    }
}
