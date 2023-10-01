<template>
    <div v-if="visibility" class="modal">
        <div class="modal-content">
            <div class="modal-body text-center">
            <div class="bouncing-loader">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div>Loading</div>
            </div>
        </div>
    </div>
</template>

<script>
 export default{
    props: ["visibility"],
 }
</script>

<style scoped>
.modal {
    display: flex;
    align-items: center;
    justify-content: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
  }
  .modal-content {
    background-color: #fefefe;
    border-radius: 8px;
    padding: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    max-width: 400px;
  }

  .modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 5px;
  }

  .close {
    font-size: 30px;
    color: #aaa;
    cursor: pointer;
  }

  .close:hover {
    color: #888;
  }

  .modal-body {
    margin-bottom: 0px;
  }
  @keyframes bouncing-loader {
  to {
    opacity: 0.1;
    transform: translate3d(0, -16px, 0);
  }
}

.bouncing-loader {
  display: flex;
  justify-content: center;
}

.bouncing-loader > div {
  width: 16px;
  height: 16px;
  margin: 1rem 0.2rem;
  background: #8385aa;
  border-radius: 50%;
  animation: bouncing-loader 0.6s infinite alternate;
}

.bouncing-loader > div:nth-child(2) {
  animation-delay: 0.2s;
}

.bouncing-loader > div:nth-child(3) {
  animation-delay: 0.4s;
}
</style>