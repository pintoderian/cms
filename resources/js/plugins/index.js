import "./axios";
import "./fontawesome";
import "bootstrap";
import Vue from "vue";

/**
 * Mensaje de error
 */
Vue.prototype.$opps_error = function () {
  this.$noty.error("Oops, No tienes acceso a esta función!");
};
/**
 * Mensaje según post, put o delete
 */
Vue.prototype.$message = function (type = null) {
  if (type == "store") {
    this.$noty.success("Genial!, Se guardo correctamente!");
  } else if (type == "update") {
    this.$noty.success("Genial!, Se actualizo correctamente!");
  } else if (type == "delete") {
    this.$noty.success("Genial!, El registro ha sido eliminado!");
  }
};
/**
 * Comprobar un permiso
 */
Vue.prototype.$permission = function (user_perms, perm) {
  return user_perms.indexOf(perm) != -1;
};
/**
 * Comprobar permiso en un array
 */
Vue.prototype.$perms_in_array = function (user_perms, array_perms) {
  let arr = [];
  for (var i = 0, len = array_perms.length; i < len; i++) {
    if (this.$permission(user_perms, array_perms[i])) {
      arr[i] = true;
    } else {
      arr[i] = false;
    }
  }
  return arr.includes(true);
};
/**
 * Comprobar permiso en la vista
 */
Vue.prototype.$check_view_perms = function (all_permissions = [], permission) {
  this.$nextTick(() => {
    if (this.$permission(all_permissions, permission)) {
      if (this.$permission(all_permissions, "access_panel")) {
        return;
      } else {
        window.location.replace("/");
      }
    } else {
      this.$router.push({ name: "dashboard" });
    }
  });
};
/**
 * Comprueba si una variable es string
 */
Vue.prototype.$isString = function (value) {
  return typeof value === 'string' || value instanceof String;
}
/**
 * Comprueba si una variable es de tipo función
 */
Vue.prototype.$isFunction = function (value) {
  var getType = {};
  return value && getType.toString.call(value) === '[object Function]';
}