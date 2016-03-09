/**
 *
 * @author: Nikolay Ermin <keltanas@gmail.com>
 */
define("news/admin/news_model", [
    'jquery',
    'backbone'
], function ($, Backbone) {
    return Backbone.Model.extend({
        //"id": "1",
        //"cat_id": "1",
        //"author_id": "1",
        //"alias": "1-sluchainyi-argument-perigeliya-v-xxi-veke",
        //"name": "Случайный аргумент перигелия в XXI веке",
        //"image": "",
        //"main": "0",
        //"priority": "0",
        //"date": "1294174800",
        //"notice": "<p>Проверка работы</p>",
        //"text": "<p>Проверка работы</p>\r\n<p>Полное описание</p>",
        //"title": "Случайный аргумент перигелия в XXI веке",
        //"keywords": "Случайный аргумент перигелия в XXI веке",
        //"description": "Случайный аргумент перигелия в XXI веке",
        //"hidden": "0",
        //"protected": "0",
        //"deleted": "0"

        defaults: {
            id: null,
            category: null,
            name: null,
            main: null,
            date: null,
            hidden: null,
            protected: null
        },

        gridColumns: [
            { label: "Id", name: "id", width: 50, key: true, search: true },
            {   label: "Категория",
                name: "category.name",
                width: 200,
                search: true,
                index: "cat_id",
                formatter:'select',
                stype: 'select',
                searchoptions: {
                    sopt:['eq'],
                    value: window.new_categories
                }
            },
            { label: "Название", name: "name", width: 300, search: true },
            { label: "Дата", name: "date", width: 100, search: false, formatter: "date" },
            { label: "Создано", name: "created_at", width: 150, search: false, formatter: "date" },
            { label: "Редакт.", name: "updated_at", width: 150, search: false, formatter: "date" },
            { label: "Главная", name: "main", width: 50, search: false },
            { label: "Скрыть", name: "hidden", width: 50, search: false },
            { label: "Защита", name: "protected", width: 50, search: false }
        ]
    });
});
