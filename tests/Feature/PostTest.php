<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase; 

    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
    }

    /** @test */
    public function authenticated_user_can_create_post()
    {
        $postData = [
            'title' => 'Test Post',
            'text' => 'Test Content'
        ];

        $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/posts', $postData)
            ->assertStatus(201)
            ->assertJson(['title' => 'Test Post']);

        $this->assertDatabaseHas('posts', ['title' => 'Test Post']);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_post()
    {
        $this->postJson('/api/posts', [
            'title' => 'Test',
            'text' => 'Content'
        ])->assertStatus(401);
    }

    /** @test */
    public function user_can_get_all_posts()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        Post::create(['title' => 'Post 1', 'text' => 'Content 1', 'user_id' => $user1->id]);
        Post::create(['title' => 'Post 2', 'text' => 'Content 2', 'user_id' => $user2->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/posts')
            ->assertStatus(200);

        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function user_can_get_their_posts()
    {
        $myUniqueTitle = 'My Unique Post ' . uniqid();
        $otherUniqueTitle = 'Other Unique Post ' . uniqid();
        
        Post::create([
            'title' => $myUniqueTitle,
            'text' => 'My Content', 
            'user_id' => $this->user->id
        ]);
        
        $otherUser = User::factory()->create();
        Post::create([
            'title' => $otherUniqueTitle,
            'text' => 'Other Content',
            'user_id' => $otherUser->id
        ]);

        echo "=== DEBUG ===\n";
        echo "Current user ID: {$this->user->id}\n";
        echo "Other user ID: {$otherUser->id}\n";
        
        $allPosts = Post::all();
        echo "All posts in DB: " . $allPosts->count() . "\n";
        foreach ($allPosts as $post) {
            echo "Post: {$post->title}, user_id: {$post->user_id}\n";
        }

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/my-posts')
            ->assertStatus(200);

        $responseData = $response->json();
        echo "API returned: " . count($responseData['data']) . " posts\n";
        foreach ($responseData['data'] as $post) {
            echo "API Post: {$post['title']}, user_id: {$post['user_id']}\n";
        }
        echo "=============\n";

        $postsData = $response->json('data');
        foreach ($postsData as $post) {
            $this->assertEquals($this->user->id, $post['user_id'], 
                "Пост '{$post['title']}' принадлежит пользователю {$post['user_id']}, а должен {$this->user->id}");
        }
    }
}