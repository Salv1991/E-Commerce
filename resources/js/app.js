import { Application } from "stimulus";
import ResponsiveNavMenu from "./controllers/responsive_nav_menu";
import Wishlist from "./controllers/wishlist";
import Filter from "./controllers/filter";
import Cart from "./controllers/cart";
import lineItemQuantity from "./controllers/lineItemQuantity";
import Order from "./controllers/order";

const application = Application.start();
application.register("responsive-nav-menu", ResponsiveNavMenu);
application.register("wishlist", Wishlist);
application.register("cart", Cart);
application.register("filter", Filter);
application.register("lineItemQuantity", lineItemQuantity);
application.register("order", Order);