// resources/js/app.js
import { Application } from "stimulus";
import HelloController from "./controllers/hello_controller";
import ResponsiveNavMenu from "./controllers/responsive_nav_menu";

const application = Application.start();
application.register("hello", HelloController);
application.register("responsive-nav-menu", ResponsiveNavMenu);