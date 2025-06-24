INSERT INTO `stores` (`id`, `name`, `description`) VALUES (NULL, 'tienda1', 'descriptcion de la tienda 1');
INSERT INTO `stores` (`id`, `name`, `description`) VALUES (NULL, 'tienda2', 'descriptcion de la tienda 2');

INSERT INTO `products` (`id`, `name`, `description`) VALUES (NULL, 'producto1', 'descripción del producto1');
INSERT INTO `products` (`id`, `name`, `description`) VALUES (NULL, 'producto2', 'descripción del producto2');
INSERT INTO `products` (`id`, `name`, `description`) VALUES (NULL, 'producto3', 'descripción del producto3');
INSERT INTO `products` (`id`, `name`, `description`) VALUES (NULL, 'producto4', 'descripción del producto4');
INSERT INTO `products` (`id`, `name`, `description`) VALUES (NULL, 'producto5', 'descripción del producto5');

INSERT INTO `stocks` (`id`, `store_id`, `product_id`, `quantity`) VALUES (NULL, '1', '1', '10');
INSERT INTO `stocks` (`id`, `store_id`, `product_id`, `quantity`) VALUES (NULL, '1', '2', '3');
INSERT INTO `stocks` (`id`, `store_id`, `product_id`, `quantity`) VALUES (NULL, '2', '3', '20');
INSERT INTO `stocks` (`id`, `store_id`, `product_id`, `quantity`) VALUES (NULL, '2', '4', '15');
INSERT INTO `stocks` (`id`, `store_id`, `product_id`, `quantity`) VALUES (NULL, '2', '5', '7');
INSERT INTO `stocks` (`id`, `store_id`, `product_id`, `quantity`) VALUES (NULL, '1', '5', '4');