<?php if (isset($_SESSION['Auth'])) { ?>

  <!-- MODAL PUBLICACION  -->

  <div class="modal fade" id="addpost" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Nueva publicación</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <img src="" style="display:none" id="post_img" class="w-100 rounded border">
          <form method="post" action="assets/php/actions.php?addpost" enctype="multipart/form-data">
            <div class="my-3">
              <input class="form-control" name="post_img" type="file" id="select_post_img">
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Texto para la publicación</label>
              <textarea name="post_text" class="form-control" id="exampleFormControlTextarea1" onclick="return validateText()" rows="4"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Publicar</button>

          </form>
        </div>

      </div>
    </div>
  </div>


  <!-- MODAL COMPARTIR PUBLICACION PUBLICACION  -->

  <!-- MODAL COMPARTIR PUBLICACION PUBLICACION -->
  <div class="modal fade" id="addshare" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Compartir publicación</h5>

          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

        </div>
        <div class="modal-body">
        <form method="post" action="assets/php/actions.php?addpostshared" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-2">
              <img src="" id="modal_picture_user_post_from" class="w-100 rounded border">

            </div>
           
              <div class="col-md-8">

                <!-- ELEMENTOS OCULTOS PARA POST EN PHP -->
                <input type="hidden" name="modal_id_post" id="modal_id_post_input">


                <!-- -->



                <p class="mb-1" style="display: none;"><strong>ID de publicacion:</strong> <span id="modal_id_post"></span></p>
                <p class="mb-1" style="display: none;"><strong>Usuario actual:</strong> <span id="modal_user_post_from"></span></p>
                <p class="mb-1"><strong>A publicar por:</strong> <span id="name_user_now"></span> <span id="last_name_user_now"></span></p>
                <p class="mb-1" style="display: none;"><strong>Usuario de public:</strong> <span id="modal_user_now"></span></p>
                <p class="mb-1"><strong>Publicación de :</strong> <span id="modal_name"></span> <span id="lastname"></span></p>
                <p class="mb-1"><strong>Texto original :</strong></p>
                <p id="text_from_post"></p>
                <p id="no_text_message" style="display: none;" class="text-danger">Este post no tiene texto.</p>


                <p class="mb-1"><strong>Imagen</strong> </p>
                <div class="col-md-12">
                  <!-- Elemento de imagen -->
                  <img src="" id="post_img_modal" class="w-100 rounded border">

                </div>

                <!-- Mensaje cuando el post no tiene una imagen -->
                <span id="no_image_message" style="display: none;" class="text-danger">Este post no tiene una imagen.</span>

                &nbsp;
                <div class="mb-3">
                  <label for="exampleFormControlTextarea2" class="form-label">Texto para la publicación</label>
                  <textarea name="post_text" class="form-control" id="exampleFormControlTextarea2" onclick="return validateText()" rows="4"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Compartir</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>






  <div class="offcanvas offcanvas-start" tabindex="-1" id="notification_sidebar" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasExampleLabel">Notiificaciones</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <?php
      $notifications = getNotifications();
      foreach ($notifications as $not) {
        $time = $not['created_at'];
        $fuser = getUser($not['from_user_id']);
        $post = '';
        if ($not['post_id']) {
          $post = 'data-bs-toggle="modal" data-bs-target="#postview' . $not['post_id'] . '"';
        }
        $fbtn = '';
      ?>
        <div class="d-flex justify-content-between border-bottom">
          <div class="d-flex align-items-center p-2">
            <div><img src="assets/images/profile/<?= $fuser['profile_pic'] ?>" alt="" height="40" width="40" class="rounded-circle border">
            </div>
            <div>&nbsp;&nbsp;</div>
            <div class="d-flex flex-column justify-content-center" <?= $post ?>>
              <a href='?u=<?= $fuser['username'] ?>' class="text-decoration-none text-dark">
                <h6 style="margin: 0px;font-size: small;"><?= $fuser['first_name'] ?> <?= $fuser['last_name'] ?></h6>
              </a>
              <p style="margin:0px;font-size:small" class="<?= $not['read_status'] ? 'text-muted' : '' ?>">@<?= $fuser['username'] ?> <?= $not['message'] ?></p>
              <time style="font-size:small" class="timeago <?= $not['read_status'] ? 'text-muted' : '' ?> text-small" datetime="<?= $time ?>"></time>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <?php
            if ($not['read_status'] == 0) {
            ?>
              <div class="p-1 bg-primary rounded-circle"></div>

            <?php

            } else if ($not['read_status'] == 2) {
            ?>
              <span class="badge bg-danger">Post Deleted</span>
            <?php
            }
            ?>

          </div>
        </div>
      <?php
      }
      ?>

    </div>
  </div>







  <div class="offcanvas offcanvas-start" tabindex="-1" id="message_sidebar" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="offcanvasExampleLabel">Messages</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="chatlist">




    </div>
  </div>

  <div class="modal fade" id="chatbox" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <a href="" id="cplink" class="text-decoration-none text-dark">
            <h5 class="modal-title" id="exampleModalLabel"><img src="assets/images/profile/default_profile.jpg" id="chatter_pic" height="40" width="40" class="m-1 rounded-circle border"><span id="chatter_name"></span>(@<span id="chatter_username">loading..</span>)</h5>
          </a>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-flex flex-column-reverse gap-2" id="user_chat">
          loading..
        </div>
        <div class="modal-footer">

          <p class="p-2 text-danger mx-auto" id="blerror" style="display:none">
            <i class="bi bi-x-octagon-fill"></i> you are not allowed to send msg to this user anymore

        </div>
        <div class="input-group p-2 " id="msgsender">
          <input type="text" class="form-control rounded-0 border-0" id="msginput" placeholder="say something.." aria-label="Recipient's username" aria-describedby="button-addon2">
          <button class="btn btn-outline-primary rounded-0 border-0" id="sendmsg" data-user-id="0" type="button">Send</button>
        </div>
      </div>
    </div>
  </div>
  </div>




<?php } ?>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/jquery.timeago.js"></script>



<script src="assets/js/custom.js?v=<?= time() ?>"></script>

<script>
  function openModal(idPost, userPostFrom, IDuserNow, nameUserNow, lastNameUserNow, pictureUserPostFrom, name, last_name, fotoPost, textPost, nameImgPostOnly) {
    // Asignar los datos a los elementos correspondientes dentro del modal
    // foto de publicacion



    // validar si hay imagen o no en el post
    if (/^\d+$/.test(nameImgPostOnly)) {
      // Si la variable fotoPost contiene solo números, el post no tiene una imagen

      document.getElementById('post_img_modal').style.display = 'none';
      document.getElementById('no_image_message').style.display = 'block';
    } else {
      document.getElementById('post_img_modal').style.display = 'block';
      document.getElementById('post_img_modal').src = fotoPost;
      document.getElementById('no_image_message').style.display = 'none';
    }

    // validar si hay texto en el post
    // Texto original

    // Texto original
    if (textPost === "" || textPost.trim() === "") {
      // Si la variable textPost está vacía o solo contiene espacios en blanco, mostrar mensaje de "Este post no tiene texto"
      document.getElementById('text_from_post').style.display = 'none';
      document.getElementById('no_text_message').style.display = 'block';
    } else {
      // Si la variable textPost tiene contenido, mostrar el texto original
      document.getElementById('text_from_post').style.display = 'block';
      document.getElementById('text_from_post').innerText = textPost;
      document.getElementById('no_text_message').style.display = 'none';
    }





    // caja de texto vacia de modal
    //document.getElementById('exampleFormControlTextarea2').value = textPost;



    // Asignar los demás datos a los elementos correspondientes dentro del modal

    // id del post
    document.getElementById('modal_id_post').innerText = idPost;

    // id del usuario que publico
    document.getElementById('modal_user_post_from').innerText = userPostFrom;

    // id del usuario actual con sesion
    document.getElementById('modal_user_now').innerText = IDuserNow;

    //  foto de perfil de usuario
    document.getElementById('modal_picture_user_post_from').src = pictureUserPostFrom;

    // usuario de la publicacion compartida
    document.getElementById('modal_name').innerText = name;


    // apellido de usuario de la publicacion compartida
    document.getElementById('lastname').innerText = last_name;

    // nombre del usuario que comparte name_user_now
    document.getElementById('name_user_now').innerText = nameUserNow;

    // apellido del usuario del perfil

    document.getElementById('last_name_user_now').innerText = lastNameUserNow;

    // texto de la publicacion text_from_post
    document.getElementById('text_from_post').innerText = textPost;



    // paso a cajas de texto para poder hacer post id del usuario que publico
    document.getElementById('modal_id_post_input').value = idPost;
   



    // Mostrar el modal utilizando JavaScript
    var modal = new bootstrap.Modal(document.getElementById('addshare'));
    modal.show();

    

  }
</script>




</body>

</html>