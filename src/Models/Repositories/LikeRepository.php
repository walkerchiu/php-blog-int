<?php

namespace WalkerChiu\Blog\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class LikeRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.blog.like'));
    }

    /**
     * @param Array  $data
     * @param Bool   $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(array $data, $auto_packing = false)
    {
        $instance = $this->instance;
        $data = array_map('trim', $data);
        $repository = $instance->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['user_id']), function ($query) use ($data) {
                                                return $query->where('user_id', $data['user_id']);
                                            })
                                            ->unless(empty($data['morph_type']), function ($query) use ($data) {
                                                return $query->where('morph_type', $data['morph_type']);
                                            })
                                            ->unless(empty($data['morph_id']), function ($query) use ($data) {
                                                return $query->where('morph_id', $data['morph_id']);
                                            });
                                })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-blog.output_format'), config('wk-blog.pagination.pageName'), config('wk-blog.pagination.perPage'));
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Like  $instance
     * @return Array
     */
    public function show($instance): array
    {
    }
}
