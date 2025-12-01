<?php

namespace App\Helpers;

class Theme
{
    /**
     * Create a new class instance.
     */
    public static function getMenu()
    {
        return self::getSideBar();
    }

    public static function getSideBar()
    {
        return [
            "dashboard" => [
                "name" => __("Dashboard"),
                "url" => route("dashboard"),
                "icon" => 'fa-solid fa-house-chimney',
                "active" => isActive("dashboard"),
                "menu" => "dashboard",
                "permission" => ["view"]
            ],
            "master" => [
                "name" => __("Masters"),
                "url" => "",
                "icon" => 'fa-solid fa-anchor',
                "active" => isActive(
                    "item-master.*",
                    "item-master.item.*",
                    "item-master.item-category.*"
                ),
                "children" => [
                    "item_category" => [
                        "name" => __("Item Category"),
                        "url" => route("master.item-category.index"),
                        "icon" => "fa-solid fa-tags",
                        "active" => isActive("master.item-category.*"),
                        "menu" => "item_category",
                        "permission" => ["view", "create"]
                    ],
                    "item" => [
                        "name" => __("Item"),
                        "url" => route("master.item.index"),
                        "icon" => "fa-solid fa-box",
                        "active" => isActive("master.item.*"),
                        "menu" => "item",
                        "permission" => ["view", "create"]
                    ],
                ]
            ],
            'users' => [
                'name' => __('Manage Users'),
                "url" => '#',
                "icon" => 'fa-solid fa-user-gear',
                "active" => isActive("users.*", "customer.*"),
                "permission" => ["view", "create"],
                "children" => [
                    "users" => [
                        "name" => __("Users"),
                        "url" => route("users.index"),
                        "icon" => "fa-solid fa-users",
                        "active" => isActive("users.index"),
                        "menu" => "users",
                        "permission" => ["view", "create", "edit", "delete"]
                    ],
                    "roles" => [
                        "name" => __("Roles & Permissions"),
                        "url" => route("users.role.index"),
                        "icon" => "fa-solid fa-user-lock",
                        "active" => isActive("users.role.*"),
                        "menu" => "role",
                        "permission" => ["view", "create", "edit", "delete"]
                    ]
                ]
            ]
        ];
    }
}
