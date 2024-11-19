import { Application } from "stimulus";
import HelloController from "./controllers/hello_controller";
import ResponsiveNavMenu from "./controllers/responsive_nav_menu";
import Wishlist from "./controllers/wishlist";
import MyCarousel from "./controllers/myCarousel";
import Filter from "./controllers/filter";

const application = Application.start();
application.register("hello", HelloController);
application.register("responsive-nav-menu", ResponsiveNavMenu);
application.register("wishlist", Wishlist);
// application.register("my-carousel", MyCarousel);
application.register("filter", Filter);