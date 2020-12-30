<template>
  <v-app>
    <div class="bg full-size">
      <div class="loading-cover full-size" v-if="loading"></div>
      <WorldMap />
      <v-snackbar
        v-model="notification"
        :color="notificationType"
        top
        :timeout="4000"
      >{{ notificationText }}</v-snackbar>
      <AddCases />
      <ViewCases />
    </div>
  </v-app>
</template>

<script>
import WorldMap from "./components/WorldMap";
import AddCases from "./components/AddCases";
import ViewCases from "./components/ViewCases";

export default {
  name: "App",

  components: {
    WorldMap,
    AddCases,
    ViewCases
  },
  data() {
    return {
      loading: true,
      notification: false,
      notificationText: null,
      notificationType: null
    };
  },
  created() {
    this.$root.$on("notify", payload => {
      this.notificationText = payload.message;
      this.notificationType = payload.type;
      this.notification = true;
    });
  },
  mounted() {
    this.loading = false;
  }
};
</script>

<style>
.bg {
  position: fixed;
  left: 0;
  top: 0;
  background-color: #14213d;
}
.loading-cover {
  position: absolute;
  z-index: 10;
  background-color: #14213d;
}

.color-white {
  color: white !important;
}

.top-left {
  position: absolute !important;
  left: 5px;
  top: 8px;
  z-index: 2;
}

.top-right {
  position: absolute !important;
  right: 20px;
  top: 8px;
  z-index: 2;
}

.full-size {
  width: 100%;
  height: 100%;
}

.full-height {
  height: 100%;
}

.map-cover {
  height: 100%;
  width: 100%;
  position: absolute;
  z-index: 1;
}
</style>
