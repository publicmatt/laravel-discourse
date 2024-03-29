<?php

namespace MatthewJensen\LaravelDiscourse\Contracts;

interface ApiClient {

    // users
    public function logoutUser(string $userName);
    public function createUser(string $name, string $userName, string $emailAddress, string $password);
    public function activateUser($userId);
    public function getUsernameByEmail($email);
    public function getUserByUsername($userName);
    public function inviteUser($email, $topicId, $userName = 'system');
    public function getUserByEmail($email);
    public function getUserBadgesByUsername($userName);

    // groups
    public function getGroups();
    public function getGroup($groupname);
    public function joinGroup($groupname, $username);
    public function getGroupIdByGroupName($groupname);
    public function leaveGroup($groupname, $username);
    public function getGroupMembers($group);
    public function addGroup($groupname, array $usernames = [], $aliaslevel = 3, $visible = 'true', $automemdomain = '', $automemretro = 'false', $title = '', $primegroup = 'false', $trustlevel = '0');
    public function removeGroup(string $groupname);


    // categories
    public function createCategory(string $categoryName, string $color = '003399', string $textColor = '636b6f', $parent_category_id = 5, string $userName = 'system');
    public function getSubCategories($parentSlug);
    public function getCategory($categoryName);
    public function getCategoryById($id);
    public function updateCat($catid, $allow_badges, $auto_close_based_on_last_post, $auto_close_hours, $background_url, $color, $contains_messages, $email_in, $email_in_allow_strangers, $logo_url, $name, $parent_category_id, $groupname, $position, $slug, $suppress_from_homepage, $text_color, $topic_template, $permissions);
    public function updateCategory($catid, $allow_badges, $auto_close_based_on_last_post, $auto_close_hours, $background_url, $color, $contains_messages, $email_in, $email_in_allow_strangers, $logo_url, $name, $parent_category_id, $groupname, $position, $slug, $suppress_from_homepage, $text_color, $topic_template, $permissions);
    public function getCategories();
    public function deleteCategory($id);

    // topics
    public function createTopic(string $topicTitle, string $bodyText, string $categoryId, string $userName, int $replyToId = 0);
    public function getTopic($topicId);
    public function topTopics($category, $period = 'daily');
    public function latestTopics($category);

    // posts
    public function createPost(string $bodyText, $topicId, string $userName);
    public function getPostsByNumber($topic_id, $post_number);
    public function updatePost($bodyhtml, $post_id, $userName = 'system');

    // tags
    public function getTag($name);
    public function getLatestTopicsForTag($name);

    // TODO:

}
