/// <reference types="jquery" />

$.fn.select2.defaults.set("language", {
  noResults: function () {
    return "Aucun résultat trouvé";
  },
  searching: function () {
    return "Recherche en cours...";
  },
});
