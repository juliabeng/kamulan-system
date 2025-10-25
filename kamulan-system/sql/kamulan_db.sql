-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2025 at 02:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kamulan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `address`, `contact`, `created_at`) VALUES
(1, 'Kamulan - Rizal, Laguna', 'Rizal, Laguna', '0917-000-0001', '2025-10-21 16:18:59'),
(2, 'Kamulan - San Pablo (P. Zulueta)', 'P. Zulueta, San Pablo City', '0917-000-0002', '2025-10-21 16:18:59'),
(3, 'Kamulan - San Pablo (A. Mabini)', 'A. Mabini, San Pablo City', '0917-000-0003', '2025-10-21 16:18:59');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `image`, `category`, `available`, `created_at`) VALUES
(5, 'Chicken Fingers with Fries - Cheddar Cheese', 'Crispy chicken fingers with cheddar cheese flavor and fries.', 114.00, 'assets/images/chicken_fingers.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(6, 'Chicken Fingers with Fries - Maple Sriracha', 'Crispy chicken fingers with maple sriracha glaze and fries.', 124.00, 'assets/images/chicken_fingers.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(7, 'Chicken Fingers with Fries - Smoked Barbecue', 'Smoky barbecue-flavored chicken fingers with fries.', 124.00, 'assets/images/chicken_fingers.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(8, 'Chicken Fingers with Fries - Garlic Glaze', 'Crispy garlic-glazed chicken fingers with fries.', 124.00, 'assets/images/chicken_fingers.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(9, 'Mozzaballs with Fries', 'Fried mashed potato balls stuffed with mozzarella cheese.', 134.00, 'assets/images/mozzaballs.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(10, '4 Cheese Quesadilla with Fries', 'Large tortilla stuffed with four kinds of cheese and Mexican sauce.', 129.00, 'assets/images/quesadilla.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(11, 'Lasagna with Fries or Garlic Bread', 'Classic baked lasagna served with fries or garlic bread.', 184.00, 'assets/images/lasagna.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(12, 'Rib Floss Quesadilla with Fries', 'Big tortilla stuffed with cheese and rib floss served with fries.', 169.00, 'assets/images/quesadilla.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(13, 'Cheese Cottage Pie', 'Mashed potato topped with cheese, corn, and carrots.', 179.00, 'assets/images/cottage_pie.jpg', 'Solo Menu', 1, '2025-10-23 13:50:37'),
(14, 'Nachos', 'Good for 2–3. Crunchy nachos with toppings.', 169.00, 'assets/images/nachos.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(15, '4 Cheese Quesadillas', 'Good for 2–3. Cheesy quesadillas with Mexican sauce.', 219.00, 'assets/images/quesadilla.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(16, 'Chicken Quesadillas', 'Good for 2–3. Cheesy quesadillas with fried chicken filling.', 219.00, 'assets/images/quesadilla.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(17, 'Rib Floss Quesadillas', 'Good for 2–3. Quesadilla stuffed with rib floss.', 254.00, 'assets/images/quesadilla.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(18, 'Chicken Fingers - Cheddar Cheese', 'Crispy chicken fingers, cheddar cheese flavor.', 184.00, 'assets/images/chicken_fingers.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(19, 'Chicken Fingers - Maple Sriracha', 'Crispy chicken fingers with maple sriracha glaze.', 204.00, 'assets/images/chicken_fingers.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(20, 'Chicken Fingers - Smoked Barbecue', 'Smoky chicken fingers.', 204.00, 'assets/images/chicken_fingers.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(21, 'Chicken Fingers - Garlic Glaze', 'Crispy garlic glazed chicken fingers.', 204.00, 'assets/images/chicken_fingers.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(22, 'Overload Fries - Cheesy Overload', 'Loaded fries with melted cheese.', 99.00, 'assets/images/fries.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(23, 'Overload Fries - Cheesy Barbecue', 'Loaded fries with barbecue and cheese.', 149.00, 'assets/images/fries.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(24, 'Overload Fries - Cheesy Mushroom', 'Loaded fries with cheese and mushrooms.', 149.00, 'assets/images/fries.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(25, 'Overload Fries - Taco Beef', 'Loaded fries with taco beef and cheese.', 149.00, 'assets/images/fries.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(26, 'Mozzaballs (7 pcs)', '7 pieces of cheesy potato balls.', 165.00, 'assets/images/mozzaballs.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(27, 'Mozzaballs (10 pcs)', '10 pieces of cheesy potato balls.', 219.00, 'assets/images/mozzaballs.jpg', 'Sharing', 1, '2025-10-23 13:50:37'),
(28, 'Chicken Fingers with Rice - Cheddar Cheese', 'With rice and mixed veggies, cheddar cheese flavor.', 149.00, 'assets/images/chicken_fingers.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(29, 'Chicken Fingers with Rice - Maple Sriracha', 'With rice and mixed veggies, maple sriracha glaze.', 155.00, 'assets/images/chicken_fingers.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(30, 'Chicken Fingers with Rice - Smoked Barbecue', 'With rice and mixed veggies, smoked barbecue flavor.', 155.00, 'assets/images/chicken_fingers.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(31, 'Chicken Fingers with Rice - Garlic Glaze', 'With rice and mixed veggies, garlic glaze flavor.', 155.00, 'assets/images/chicken_fingers.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(32, 'Classic Chicken Fingers', 'Served with rice, egg, and gravy.', 155.00, 'assets/images/chicken_fingers.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(33, 'Fried Chicken Thigh Fillet', 'Deep-fried chicken thigh fillet with rice and gravy.', 194.00, 'assets/images/chicken.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(34, 'Country Fried Porkchop', 'Deep-fried pork chop with rice and gravy.', 199.00, 'assets/images/porkchop.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(35, 'Chicken Katsu', 'Breaded chicken fillet with sesame rice and katsu salad.', 199.00, 'assets/images/katsu.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(36, 'Tonkatsu (Pork)', 'Breaded pork cutlet with sesame rice and katsu salad.', 249.00, 'assets/images/katsu.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(37, 'Prawn Cutlet', '4 pieces of prawn with sesame rice and katsu salad.', 249.00, 'assets/images/prawn.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(38, 'Will Combo Meal', 'Combo: Mozzaballs, Turones/Fries, Chicken Fingers with Rice.', 219.00, 'assets/images/combo.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(39, 'Burger Steak', 'Burger patty with rice, mushroom gravy, and veggies.', 219.00, 'assets/images/burger_steak.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(40, 'Seared Pepper Pork Steak', 'Served with java rice, veggies, and mushroom gravy.', 219.00, 'assets/images/pork_steak.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(41, 'Seared Pork Belly', 'Served with java rice, veggies, and mushroom gravy.', 219.00, 'assets/images/pork_belly.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(42, 'Baby Back Ribs', 'Slow-cooked pork ribs with java rice or fries.', 319.00, 'assets/images/ribs.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(43, 'Burger Steak on Cottage Pie', 'Burger steak topped with cottage pie.', 359.00, 'assets/images/burger_cottage.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(44, 'Baby Back Ribs with Mozzaballs', 'Ribs paired with cheesy mozzaballs.', 349.00, 'assets/images/ribs_mozza.jpg', 'Main Dish', 1, '2025-10-23 13:50:37'),
(45, 'Chicken Burger with Fries', 'Deep-fried chicken fillet with lettuce, tomato, and ranch sauce.', 179.00, 'assets/images/chicken_burger.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(46, '4 Cheese Burger with Fries', 'Four kinds of cheese with lettuce, tomato, and ranch sauce.', 199.00, 'assets/images/cheese_burger.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(47, 'Mushroom Burger with Fries', 'Sautéed mushroom, melted cheese, cheddar sauce, lettuce, tomato, and ranch.', 199.00, 'assets/images/mushroom_burger.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(48, 'Taco Dog with Fries', 'Giant hotdog with taco and cheddar sauce.', 169.00, 'assets/images/taco_dog.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(49, 'Mushroom Dog with Fries', 'Giant hotdog with sautéed mushroom and cheddar sauce.', 169.00, 'assets/images/mushroom_dog.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(50, 'Cheese Roll Dog with Fries', 'Giant hotdog wrapped with four cheese and Mexican sauce.', 179.00, 'assets/images/cheese_dog.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(51, 'Quesadilla Burger with Fries', '140g burger patty wrapped in tortilla with tomato, lettuce, and mozzarella.', 254.00, 'assets/images/quesadilla_burger.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(52, 'Rib Floss Burger with Truffle Mayo', 'Savory rib floss burger with truffle mayo.', 209.00, 'assets/images/rib_burger.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(53, 'Bacon Barbecue Burger', 'Juicy burger topped with bacon and barbecue sauce.', 209.00, 'assets/images/bacon_burger.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(54, 'Taco Burger with Fries', 'Burger with taco sauce and fries on the side.', 209.00, 'assets/images/taco_burger.jpg', 'Sandwich', 1, '2025-10-23 13:51:46'),
(55, 'Turon de Keso', '10 pcs. turon with milk syrup and cheese.', 84.00, 'assets/images/turon_keso.jpg', 'Dessert', 1, '2025-10-23 13:51:46'),
(56, 'Turon de Choco', '10 pcs. turon with chocolate sauce.', 84.00, 'assets/images/turon_choco.jpg', 'Dessert', 1, '2025-10-23 13:51:46'),
(57, 'Churros', '150g churros with chocolate sauce.', 89.00, 'assets/images/churros.jpg', 'Dessert', 1, '2025-10-23 13:51:46'),
(58, 'Chocolate Ice Cream', '3 scoops with chocolate syrup and chocolate chips.', 89.00, 'assets/images/choco_icecream.jpg', 'Dessert', 1, '2025-10-23 13:51:46'),
(59, 'Strawberry Ice Cream', '3 scoops with strawberry syrup, grahams, and mallows.', 99.00, 'assets/images/strawberry_icecream.jpg', 'Dessert', 1, '2025-10-23 13:51:46'),
(60, 'Nachos Platter', 'Corn flakes with taco beef sauce, tomato, lettuce, and jalapeño pickles.', 449.00, 'assets/images/nachos_platter.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(61, '4 Cheese Quesadillas Platter', '4 big tortillas stuffed with cheese and Mexican sauce.', 469.00, 'assets/images/quesadilla_platter.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(62, 'Mozzaballs Platter', 'Large serving of cheesy potato balls.', 549.00, 'assets/images/mozzaballs_platter.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(63, 'Chicken Quesadilla Platter', '4 big tortillas with fried chicken and Mexican sauce.', 789.00, 'assets/images/chicken_quesadilla_platter.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(64, 'Chicken Fingers Platter', 'Chicken strips with cheddar, maple sriracha, and garlic glaze.', 549.00, 'assets/images/chicken_fingers_platter.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(65, 'Chicken Katsu Platter', 'Breaded chicken fillet with salad and sauce.', 599.00, 'assets/images/chicken_katsu_platter.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(66, 'Tonkatsu (Pork) Platter', 'Pork cutlet with salad and sauce.', 659.00, 'assets/images/tonkatsu_platter.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(67, 'Baby Back Ribs Platter', 'Slow-cooked pork ribs with BBQ sauce, onion, and sausage.', 1199.00, 'assets/images/ribs_platter.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(68, 'Lasagna Half Tray (6-8 pax)', 'Half tray serving of classic lasagna.', 1109.00, 'assets/images/lasagna_half.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(69, 'Lasagna Full Tray (12-15 pax)', 'Full tray serving of lasagna.', 1899.00, 'assets/images/lasagna_full.jpg', 'Platters', 1, '2025-10-23 13:51:46'),
(70, 'Plain Rice', 'Single serving of plain rice.', 28.00, 'assets/images/plain_rice.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(71, 'Java Rice', 'Flavored rice with garlic and spices.', 48.00, 'assets/images/java_rice.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(72, 'Sesame Rice', 'Rice cooked with sesame flavor.', 38.00, 'assets/images/sesame_rice.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(73, 'Mozzaballs Add-on', 'Extra piece of mozzarella ball.', 24.00, 'assets/images/mozzaballs.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(74, 'Garlic Bread', 'Single piece of garlic bread.', 15.00, 'assets/images/garlic_bread.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(75, 'Small Sauce', 'Small serving of sauce.', 18.00, 'assets/images/sauce_small.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(76, 'Big Sauce', 'Large serving of sauce.', 43.00, 'assets/images/sauce_big.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(77, 'Solo Fries', 'Single serving of fries.', 49.00, 'assets/images/fries.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(78, 'Katsu Salad', 'Side serving of katsu salad.', 33.00, 'assets/images/katsu_salad.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(79, 'Mixed Veggies', 'Side serving of mixed vegetables.', 33.00, 'assets/images/mixed_veggies.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(80, 'Mushroom Gravy', 'Single serving of mushroom gravy.', 28.00, 'assets/images/mushroom_gravy.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(81, 'Gravy', 'Classic Kamulan gravy.', 23.00, 'assets/images/gravy.jpg', 'Add-ons', 1, '2025-10-23 13:51:46'),
(82, 'Aloe Vera Pitcher', 'Refreshing aloe vera drink (pitcher).', 194.00, 'assets/images/aloe_vera_drink.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(83, 'Pomegranate Pitcher', 'Fruity pomegranate drink (pitcher).', 194.00, 'assets/images/pomegranate_drink.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(84, 'Mixed Berries & Grapes Pitcher', 'Pitcher of berry-grape goodness.', 194.00, 'assets/images/berries_grapes.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(85, 'Mango Pitcher', 'Sweet mango drink (pitcher).', 144.00, 'assets/images/mango_drink.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(86, 'Four Season Pitcher', 'Refreshing four-season fruit blend.', 134.00, 'assets/images/four_season.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(87, 'Pineapple Pitcher', 'Fresh pineapple juice.', 134.00, 'assets/images/pineapple.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(88, 'Coke 1.5L', '1.5-liter Coca-Cola.', 80.00, 'assets/images/coke15.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(89, 'Coke 1.5L Zero Sugar', '1.5-liter Coke Zero.', 100.00, 'assets/images/cokezero15.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(90, 'Affogato', 'Espresso with vanilla ice cream.', 99.00, 'assets/images/affogato.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(91, 'Iced Coffee with Vanilla Ice Cream', 'Iced coffee with vanilla ice cream topping.', 139.00, 'assets/images/iced_coffee_vanilla.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(92, 'Iced Coffee (No Ice Cream)', 'Plain iced coffee.', 129.00, 'assets/images/iced_coffee.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(93, 'Cold Chocolate', 'Cold chocolate drink.', 55.00, 'assets/images/cold_choco.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(94, 'Hot Chocolate', 'Hot choco with milk.', 55.00, 'assets/images/hot_choco.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(95, 'Hot Latte', 'Classic hot latte.', 100.00, 'assets/images/latte.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(96, 'Black Americano', 'Strong brewed black coffee.', 55.00, 'assets/images/americano.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(97, 'Four Season (Solo)', 'Single serving of four season drink.', 59.00, 'assets/images/four_season_solo.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(98, 'Pineapple (Solo)', 'Single serving pineapple juice.', 59.00, 'assets/images/pineapple_solo.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(99, 'Fresh Lemon', 'Freshly squeezed lemon juice.', 54.00, 'assets/images/lemon.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(100, 'Coke (Can)', 'Canned Coca-Cola.', 24.00, 'assets/images/coke_can.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(101, 'Sprite (Can)', 'Canned Sprite.', 24.00, 'assets/images/sprite_can.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(102, 'Royal (Can)', 'Canned Royal.', 24.00, 'assets/images/royal_can.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(103, 'Wilkins Water', 'Bottled Wilkins drinking water.', 22.00, 'assets/images/wilkins.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(104, 'Red Horse Beer', 'Beer in can.', 79.00, 'assets/images/redhorse.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(105, 'San Mig Light', 'Light beer in can.', 79.00, 'assets/images/sanmiglight.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(106, 'San Mig Pale Pilsen', 'Classic Pale Pilsen beer.', 79.00, 'assets/images/sanmigpale.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(107, 'San Mig Apple', 'Apple-flavored beer.', 79.00, 'assets/images/sanmigapple.jpg', 'Drinks', 1, '2025-10-23 13:51:46'),
(108, 'Mango Shake Graham', 'Creamy mango shake with graham.', 170.00, 'assets/images/mango_shake.jpg', 'Drinks', 1, '2025-10-23 13:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `payment_method` enum('COD','GCash') DEFAULT 'COD',
  `status` enum('Placed','Preparing','Out for Delivery','Delivered','Cancelled') DEFAULT 'Placed',
  `rider_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `branch_id`, `address`, `total`, `payment_method`, `status`, `rider_id`, `created_at`) VALUES
(1, 4, 1, 'Rizal, Laguna', 114.00, 'COD', 'Delivered', NULL, '2025-10-22 14:11:04'),
(2, 4, 1, NULL, 268.00, 'COD', 'Delivered', NULL, '2025-10-23 15:37:03');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `qty`, `price`) VALUES
(1, 1, 2, 1, 84.00),
(2, 2, 5, 1, 114.00),
(3, 2, 10, 1, 129.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(120) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('buyer','staff','rider','manager') DEFAULT 'buyer',
  `branch_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `branch_id`, `created_at`) VALUES
(1, 'Manager', 'manager@kamulan.com', '09170000000', '$2y$10$dQdP2RQC02ox05NxUeEEM.8U48797kKnWmmCuZkXsEXoFr4sWaM6i', 'manager', NULL, '2025-10-21 16:18:59'),
(2, 'Juan Rizal', 'juan_rizal@rider.com', '09170000001', '$2y$10$dQdP2RQC02ox05NxUeEEM.8U48797kKnWmmCuZkXsEXoFr4sWaM6i', 'rider', 1, '2025-10-21 16:18:59'),
(3, 'Ana Mabini', 'ana_mabini@staff.com', '09170000002', '$2y$10$dQdP2RQC02ox05NxUeEEM.8U48797kKnWmmCuZkXsEXoFr4sWaM6i', 'staff', 3, '2025-10-21 16:18:59'),
(4, 'Customer', 'customer@gmail.com', '09170000003', '$2y$10$dQdP2RQC02ox05NxUeEEM.8U48797kKnWmmCuZkXsEXoFr4sWaM6i', 'buyer', NULL, '2025-10-21 16:18:59'),
(5, 'Jane Cruz', 'jane_rizal@staff.com', '09123456789', '$2y$10$dQdP2RQC02ox05NxUeEEM.8U48797kKnWmmCuZkXsEXoFr4sWaM6i', 'staff', 1, '2025-10-23 15:39:33'),
(6, 'Eljoysa Tajao', 'eljoysa@gmail.com', '09456123456', '$2y$10$a7Lw4k9dk3VstkjyCzK76.my/rh7MpupFulxivDII7CbHtgYnxdRm', 'buyer', NULL, '2025-10-25 09:13:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
