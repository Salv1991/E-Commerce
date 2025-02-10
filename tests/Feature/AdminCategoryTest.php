<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminCategoryTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_category_edit_changes_are_saved_successfully() {
        $user = User::create([
            'name' => 'Michael Scott',    
            'email' => 'michaelScott@example.com',
            'password' => '12341312',
            'is_admin' => true
        ]);

        $this->actingAs($user);

        $category = Category::create([
            'parent_id' => null,
            'depth' => 2,
            'weight' => 2,
            'slug' => 'foo',
            'title' => 'foo',
            'description' => 'Sittin in my office with a plate of grilled bacon Called my man Dwight, just to see what was shakin Yo, Mike, our town is dope and prettySo check out how we live In the Electric City',
            'image_path' => 'products/image7.jpg' 
        ]);

        $response = $this->get(route('admin.category.edit.show', $category->id));

        $response->assertStatus(200);
        
        $response->assertSee($category->title);
        
        $this->patch(route('admin.category.edit.store',  ['id' => $category->id]),[
            'weight' => 3,
            'title' => 'foobar',
            'description' => 'lorem description'
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('categories', ([
            'parent_id' => $category->parent_id,
            'depth' => $category->depth,
            'weight' => 3,
            'slug' => 'foobar',
            'title' => 'foobar',
            'description' => 'lorem description',
            'image_path' => $category->image_path 
        ]));
    }

    public function test_category_try_to_edit_changes_with_empty_fields() {
        $user = User::create([
            'name' => 'Michael Scott',    
            'email' => 'michaelScott@example.com',
            'password' => '12341312',
            'is_admin' => true
        ]);

        $this->actingAs($user);

        $category = Category::create([
            'parent_id' => null,
            'depth' => 2,
            'weight' => 2,
            'slug' => 'foo',
            'title' => 'foo',
            'description' => 'Sittin in my office with a plate of grilled bacon Called my man Dwight, just to see what was shakin Yo, Mike, our town is dope and prettySo check out how we live In the Electric City',
            'image_path' => 'products/image7.jpg' 
        ]);

        $response = $this->get(route('admin.category.edit.show', $category->id));

        $response->assertStatus(200);
        
        $response->assertSee($category->title);
        
        $this->patch(route('admin.category.edit.store',  ['id' => $category->id]),[
            'weight' => '',
            'title' => '',
            'description' => ''
        ]);

        $response->assertSessionHasErrors(['title', 'weight', 'description']);
        
        $this->assertDatabaseHas('categories', ([
            'parent_id' => $category->parent_id,
            'depth' => $category->depth,
            'weight' => $category->weight,
            'slug' => $category->slug,
            'title' => $category->title,
            'description' => $category->description,
            'image_path' => $category->image_path 
        ]));
    }

    public function test_create_new_category_successfully() {
        $user = User::create([
            'name' => 'Michael Scott',    
            'email' => 'michaelScott@example.com',
            'password' => '12341312',
            'is_admin' => true
        ]);

        $category = Category::create([
            'parent_id' => null,
            'depth' => 0,
            'weight' => 2,
            'slug' => 'foobar',
            'title' => 'foobar',
            'description' => 'Sittin in my office with a plate of grilled bacon Called my man Dwight, just to see what was shakin Yo, Mike, our town is dope and prettySo check out how we live In the Electric City',
            'image_path' => 'products/placeholder.jpg'  
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.category.create.show'));

        $response->assertStatus(200);
                
        $this->post(route('admin.category.create.store'),[
            'parent_id' => $category->id,
            'weight' => 2,
            'title' => 'foo',
            'description' => 'Sittin in my office with a plate of grilled bacon Called my man Dwight, just to see what was shakin Yo, Mike, our town is dope and prettySo check out how we live In the Electric City',
        ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('categories', ([
            'parent_id' => $category->id,
            'depth' => 1,
            'weight' => 2,
            'slug' => 'foo',
            'title' => 'foo',
            'description' => 'Sittin in my office with a plate of grilled bacon Called my man Dwight, just to see what was shakin Yo, Mike, our town is dope and prettySo check out how we live In the Electric City',
            'image_path' => 'products/placeholder.jpg' 
        ]));
    }
}
