$(document).ready(function () {
    let $parentContainer = $('.choose-category .category-list .category-content-parent-bulk'),
        $childrenContainer = $('.choose-category .category-list .category-content-children-bulk'),
        $childrenParentContainer = $('.choose-category .category-list .category-content-parent-children-bulk'),
        $parent = $('.choose-category .category-list .category-content-parent-bulk .category-content-parent.child');

    $parent.click(function (e) {
        e.preventDefault();

        let $this = $(this),
            $parent = getParent($this.find('.category-parent')),
            $categories = getCategories($this.find('.category-children'));

        $childrenParentContainer.html("");
        $childrenContainer.html("");

        $childrenParentContainer.append(parentView($parent));

        $.map($categories, function (element) {
            $childrenContainer.append(categoryView(element, $parent));
        });

        $parentContainer.addClass('d-none');
        $childrenParentContainer.removeClass('d-none');
        $childrenContainer.removeClass('d-none');

        $('.category-content-parent-end').click(function () {
            $parentContainer.removeClass('d-none');
            $childrenParentContainer.addClass('d-none');
            $childrenContainer.addClass('d-none');
        });
    });
});

function parentView(category) {
    return $('<div data-id="' + category.id + '" class="col-12 col-md-4 col-lg-3 mb-lg-4 mb-3 category-content-parent-end">' +
        '<div class="list-group">' +
            '<a class="list-group-item d-flex align-items-center">' +
                '<div class="advert-image">' +
                    '<img src="' + category.url + '" class="img-fluid" alt="Category image">' +
                '</div>'+
                '<div class="title">' + category.name + '</div>' +
                '<i class="fas fa-long-arrow-alt-left ml-auto"></i>' +
            '</a>' +
        '</div>' +
    '</div>');
}

function categoryView(category, parent) {
    let route = Routing.generate('app_advert_create', {category_slug: parent.slug, c: category.slug});

    return $('<div data-id="' + category.id + '" class="col-12 col-md-4 col-lg-3 mb-4">' +
        '<div class="list-group">' +
            '<a href="' + route + '" class="list-group-item d-flex align-items-center">' +
                '<div class="title children">' + category.name + '</div>' +
                '<i class="fas fa-long-arrow-alt-right ml-auto"></i>' +
            '</a>' +
        '</div>' +
    '</div>');
}

function getCategories(element) {
    let categories = [];

    const ids = element.attr('data-id').split('|');
    const names = element.attr('data-name').split('|');
    const slugs = element.attr('data-slug').split('|');

    for (let i = 0; i < ids.length; i++) {
        categories.push({id: ids[i], name: names[i], slug: slugs[i]})
    }

    return categories;
}

function getParent(element) {
    return {
        id: element.attr('data-id'),
        name: element.attr('data-name'),
        slug: element.attr('data-slug'),
        url: element.attr('data-url')
    };
}