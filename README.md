
# iBoo Fullstack Challenge

## Introduction

Although I have experience using Laravel, this is my first time using Symfony so I don't have some concepts and best practices of this framework at hand. Still, I have tried to make it as solid and robust as possible.

## Documentation

You can find all the documentation about the API and its endpoints [here](https://documenter.getpostman.com/view/12623615/2s93CHuEys). The documentation has been done using Postman.


## The Project

I have made a small api with a CRUD of products and categories for the products, and a basic view where, calling the API through an AJAX request, we can see a list of all the products created and their basic information, as well as search for products through matching the name, description or its identifier in real time.

To begin with, it is necessary to create the categories, which are only composed of an identifier and a name because when creating the products it is mandatory to assign a category.

### Category

`id`

`name`

To keep the project simple, I have used a simple autoincremental identifier for both products and categories, but in a real project it would be safer to use a uuid or a random alphanumeric id.

In this case, moreover. It would be good to automatically create a default category, which in case you do not specify one when creating the product, it will be assigned the default category.

In our simple case, once the categories have been created, we can start creating the products through the API. To create the products, we must, as a minimum, specify the necessary data:

### Product

`name`

`description`

`weight`

`category`

And optionally, the data that can be null.

`enabled`

`img`

Without the mandatory data we will not be able to create the product.

Once the products have been created, we can use all the endpoints defined in the API documentation to retrieve all of them that are enabled, retrieve a specific one, search by matching name, description or id, update an existing product or delete a product. In addition, we can access the view to see the existing products and search for products through the search engine. The products are loaded through ajax using the API made, and the search engine also uses ajax to load matching products through the API.

In this case, all the products in the database are returned directly, but in a real case, it would be good to put a limit of products and make a pagination, so as not to overload the API responses.

In a real project, error handling and validation of all data should be much more extensive and deep, but in this case I have kept it simple.

Thanks for your time!

## Author

- [Marc Martínez Mascarell](https://www.linkedin.com/in/marc-martinez-mascarell/)

