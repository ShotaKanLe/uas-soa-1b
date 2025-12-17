<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\News;
use App\Http\Controllers\NewsController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config; 

class NewsControllerUnitTest extends TestCase
{
    use RefreshDatabase; 
    protected $user; 
    public function setUp(): void
    {
        parent::setUp();
        
        $key = 'base64:Kr76Mzt6+EoDJMrWQpntmw3Q8E49IqFp320BbLolPzI='; 
        
        Config::set('app.key', $key);
        Config::set('app.cipher', 'aes-256-cbc');

        $this->user = \App\Models\User::factory()->create();
    }

    public function test_old_image_is_deleted_on_news_update()
    {
        Storage::fake('public'); 
        
        $oldImageFile = UploadedFile::fake()->image('old_image.jpg');
        $oldImagePath = $oldImageFile->store('news_images', 'public');
        $news = News::factory()->create(['image' => $oldImagePath]);
        
        Storage::disk('public')->assertExists($oldImagePath); 

        $newImageFile = UploadedFile::fake()->image('new_image.png');
        
        $requestData = [
            'title' => 'Updated Title',
            'content' => 'Updated content with minimum length.',
            'publisher' => 'Updated Publisher',
            '_method' => 'PUT', 
        ];

        $this->actingAs($this->user)
             ->put(route('news.update', $news->id), $requestData + ['image' => $newImageFile]);

        Storage::disk('public')->assertMissing($oldImagePath); 
        Storage::disk('public')->assertExists('news_images/' . $newImageFile->hashName()); 
    }

    public function test_image_is_deleted_on_news_delete()
    {
        Storage::fake('public'); 
        
        $imageFile = UploadedFile::fake()->image('deletable_image.jpg');
        $imagePath = $imageFile->store('news_images', 'public');
        $news = News::factory()->create(['image' => $imagePath]);
        
        Storage::disk('public')->assertExists($imagePath); 

        $this->actingAs($this->user) 
             ->delete(route('news.delete', $news->id));

        Storage::disk('public')->assertMissing($imagePath); 
        $this->assertDatabaseMissing('news', ['id' => $news->id]); 
    }
}