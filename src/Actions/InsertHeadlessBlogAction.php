<?php

namespace SynergizeFlow\Blog\Actions;

use Illuminate\Support\Str;
use SynergizeFlow\Blog\Models\Category;
use SynergizeFlow\Blog\Models\Post;
use SynergizeFlow\Laravel\Contracts\InsertBlogContract;

class InsertHeadlessBlogAction implements InsertBlogContract
{
    /**
     * Handle the insertion of AI-generated blog content into sf_posts.
     *
     * @param  array<string, mixed>  $payload
     */
    public function execute(array $payload): mixed
    {
        $title = $payload['title'] ?? 'Untitled Post';
        $content = $payload['content'] ?? '';
        $excerpt = $payload['excerpt'] ?? Str::limit(strip_tags($content), 160);
        $featuredImage = $payload['featured_image'] ?? $payload['image'] ?? null;
        $authorId = $payload['author_id'] ?? $payload['wp_author_id'] ?? null;

        // Resolve or create category if category name or ID is provided
        $categoryId = null;
        if (! empty($payload['category_id'])) {
            $categoryId = (int) $payload['category_id'];
        } elseif (! empty($payload['category_name']) || ! empty($payload['category'])) {
            $categoryName = (string) ($payload['category_name'] ?? $payload['category']);
            $category = Category::firstOrCreate(
                ['name' => $categoryName],
                ['slug' => Str::slug($categoryName)]
            );
            $categoryId = $category->id;
        }

        // Create the post
        $post = Post::create([
            'title' => $title,
            'slug' => ! empty($payload['slug']) ? Str::slug($payload['slug']) : Str::slug($title),
            'content' => $content,
            'excerpt' => $excerpt,
            'featured_image' => $featuredImage,
            'category_id' => $categoryId,
            'author_id' => $authorId,
            'status' => $payload['status'] ?? 'published',
            'published_at' => now(),
        ]);

        return [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'category_id' => $post->category_id,
            'status' => $post->status,
        ];
    }
}
