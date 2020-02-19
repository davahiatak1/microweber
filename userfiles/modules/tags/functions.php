<?php

api_expose_admin('get_post_tags', function($params) {

    $post = get_content([
        'id'=>$params['post_id'],
        'single'=> '1'
    ]);

    $tags = db_get('tagging_tagged', [
        'taggable_id'=>$post['id']
    ]);

    return array('title'=>$post['title'], 'tags'=>$tags);
});

api_expose_admin('tag/view', function($params) {

    $tag_id = $params['tag_id'];
    $filter = [
        'no_cache'=>false,
        'id'=>$tag_id,
        'single'=>1
    ];
    $tag = db_get('tagging_tags', $filter);

    return $tag;

});

api_expose_admin('tag/edit', function($params) {

    if (empty(trim($params['name'])) || empty(trim($params['slug']))) {
        return ['status'=>false];
    }

    $newData = [];
    $newData['name'] = $params['name'];
    $newData['slug'] = $params['slug'];

    if (isset($params['tag_id'])) {
        $tag_id = $params['tag_id'];
        $tag = db_get('tagging_tags', [
            'no_cache'=>false,
            'id'=>$tag_id,
            'single'=>1
        ]);
        if ($tag) {
            $newData['id'] = $tag['id'];
        }
    }

    $tagSaved = db_save('tagging_tags',$newData);

    if ($tagSaved) {
        if (!isset($newData['id'])) {
            $newData['id'] = $tagSaved;
        }

        return $newData;
    }

    return ['status'=>false];

});

api_expose_admin('tag/delete', function($params) {

    $tag_id = $params['tag_id'];
    $filter = [
        'no_cache'=>false,
        'id'=>$tag_id,
        'single'=>1
    ];
    $tag = db_get('tagging_tags', $filter);
    if ($tag) {
        if (db_delete('tagging_tags', $tag_id)) {
            echo json_encode(['status'=>true]);
            exit;
        }
    }

    echo json_encode(['status'=>false]);
    exit;
});

api_expose_admin('post_tag/edit', function($params) {

    if (empty(trim($params['tag_name'])) || empty(trim($params['tag_slug']))) {
        return ['status'=>false];
    }

    return db_save('tagging_tagged', [
       'id'=>$params['id'],
       'tag_name'=>$params['tag_name'],
       'tag_slug'=>$params['tag_slug'],
    ]);

});

api_expose_admin('post_tag/delete', function($params) {

    $post_tag_id = $params['post_tag_id'];
    $filter = [
        'no_cache'=>false,
        'id'=>$post_tag_id,
        'single'=>1
    ];
    $tag = db_get('tagging_tagged', $filter);
    if ($tag) {
        if (db_delete('tagging_tagged', $post_tag_id)) {
            echo json_encode(['status'=>true]);
            exit;
        }
    }

    echo json_encode(['status'=>false]);
    exit;
});