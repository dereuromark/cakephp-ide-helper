<?php

namespace Cake\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class PostsFixture extends TestFixture
{
    /**
     * @var array
     */
    public array $records = [
        ['author_id' => 1, 'title' => 'First Post', 'body' => 'First Post Body', 'published' => 'Y'],
        ['author_id' => 3, 'title' => 'Second Post', 'body' => 'Second Post Body', 'published' => 'Y'],
        ['author_id' => 1, 'title' => 'Third Post', 'body' => 'Third Post Body', 'published' => 'Y'],
    ];
}
