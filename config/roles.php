<?php
return [
    'super_admin'    => ['*'],                          // all permissions
    'admin'          => [
        'manage_services', 'manage_portfolio',
        'manage_blog', 'manage_messages', 'publish_articles',
    ],
    'article_writer' => [
        'create_article', 'edit_own_article', 'submit_for_review',
    ],
];