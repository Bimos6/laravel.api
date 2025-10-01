<?php

namespace App\Orchid\Layouts\Post;

use App\Models\Post;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Layouts\Table;

class PostListLayout extends Table
{
    /**
     * Data source.
     *
     * @var string
     */
    public $target = 'posts';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('title', 'Title')
                ->sort()
                ->filter(TD::FILTER_TEXT)
                ->render(function (Post $post) {
                    return Link::make($post->title)
                        ->route('platform.post.edit', $post);
                }),

            TD::make('created_at', 'Created')
                ->sort()
                ->render(function (Post $post) {
                    return $post->created_at->format('Y-m-d H:i');
                }),

            TD::make('updated_at', 'Last edit')
                ->sort()
                ->render(function (Post $post) {
                    return $post->updated_at->format('Y-m-d H:i');
                }),

            TD::make('text', 'Text')
                ->render(function (Post $post) {
                    $cleanText = strip_tags($post->text);
                    $cleanText = trim($cleanText);
                    $cleanText = preg_replace('/\s+/', ' ', $cleanText);
                    
                    if (empty($cleanText) || $cleanText === ' ') {
                        return '<span style="color: #999; font-style: italic;">No content</span>';
                    }
                    
                    $preview = strlen($cleanText) > 100 ? substr($cleanText, 0, 100) . '...' : $cleanText;
                    
                    return '<div style="max-width: 300px; white-space: normal;">' . e($preview) . '</div>';
                }),

            TD::make('user_id', 'Author')
                ->sort()
                ->render(function (Post $post) {
                    return $post->user->name;
                }),
        ];
    }
}