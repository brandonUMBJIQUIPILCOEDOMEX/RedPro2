<?php
global $user;
global $posts;
global $follow_suggestions;

// invoca a nuestro controlador
//require_once("c://xampp/htdocs/redpro/assets/php/functions.php");
//$obj = new modelo();


?>






<div class=" container mt-2 p-2"></div>

<div class="container mt-5 col-md-10 col-sm-12 col-lg-9 rounded-0 d-flex justify-content-between">
    <div class="col-md-8 col-sm-12" style="max-width:93vw">

        <?php

        showError('post_img');
        if (count($posts) < 1) {
            echo "<p class='p-2 bg-white border rounded text-center my-3 col- mx-auto'>Sin publicaciones</p>";
        }



        foreach ($posts as $post) {
            $likes = getLikes($post['id']);
            $comments = getComments($post['id']);

            /*
           echo $post['uid'];
           $a=showPostShared($post['uid']);
           print_r($a);
           */







        ?>
            <div class="card mt-4">
                <div class="card-title d-flex justify-content-between  align-items-center">



                    <div class="d-flex align-items-center p-2">
                        <img src="assets/images/profile/<?= $post['profile_pic'] ?>" alt="" height="30" width="30" class="rounded-circle border">&nbsp;&nbsp; <a href='?u=<?= $post['username'] ?>' class="text-decoration-none text-dark"><?= $post['first_name'] ?> <?= $post['last_name'] ?></a>
                    </div>







                    <!-- /* ============= ELIMINAR PUBLICACION ============= */-->

                    <div class="p-2">
                        <?php
                        if ($post['uid'] == $user['id']) {
                        ?>

                            <div class="dropdown">

                                <i class="bi bi-three-dots-vertical" id="option<?= $post['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false"></i>

                                <ul class="dropdown-menu" aria-labelledby="option<?= $post['id'] ?>">
                                    <li><a class="dropdown-item" href="assets/php/actions.php?deletepost=<?= $post['id'] ?>"><i class="bi bi-trash-fill"></i> Eliminar Post</a></li>
                                </ul>
                            </div>
                        <?php
                        }
                        ?>

                    </div>
                </div>






                <!-- /* ============= TEXTO DE PUBLICACION ============= */-->

                <?php
                if ($post['post_text']) {
                ?>
                    <div class="card-body">
                        <?= $post['post_text'] ?>
                    </div>
                <?php
                }
                ?>






                <?php


                $isShared = showPostShared($post['id']);

                if (isset($isShared) && isset($isShared[0]['shared_post_id'])) {

                    $shared_post_id = $isShared[0]['shared_post_id'];
                    //print_r($isShared);


                    if ($shared_post_id != 0) {
                        // echo "es compartida";    ----> consulta a la otra publicacion

                        $SharedPostContent = showPostSharedContent($shared_post_id);

                        

                        if (isset($SharedPostContent) && !empty($SharedPostContent)) {
                            


                            $id_userS = $SharedPostContent[0]['user_id'];



                            $userDates = showPostSharedUser($id_userS);

                            if (isset($userDates)) {

                ?>


                                <div class="card mt-4">
                                    <div class="card-title d-flex justify-content-between  align-items-center">



                                        <div class="d-flex align-items-center p-2">
                                            <img src="assets/images/profile/<?= $userDates[0]['profile_pic'] ?>" alt="" height="30" width="30" class="rounded-circle border">&nbsp;&nbsp; <a href='?u=<?= $userDates[0]['username'] ?>' class="text-decoration-none text-dark"><?= $userDates[0]['first_name'] ?> <?= $userDates[0]['last_name'] ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php





                                if ($SharedPostContent[0]['post_text']) {
                                ?>
                                    <div class="card-body">
                                        <?= $SharedPostContent[0]['post_text'] ?>
                                    </div>
                                <?php
                                }
                            }


                            if (preg_match('/^[0-9]+$/', $SharedPostContent[0]['post_img']) || (empty($SharedPostContent[0]['post_img']))) {

                                // publicacion sin imagen
                                ?>


                            <?php
                            } else {
                            ?>
                                <!--// publicacion con imagen  puede ir texto arriba de la imagen -->
                                <img src="assets/images/posts/<?= $SharedPostContent[0]['post_img'] ?>" loading=lazy class="" alt="...">

                            <?php
                            }
                            ?>









                <?php


                        }
                    } else if ($shared_post_id == 0) {
                    }
                }

                ?>








                <!-- /* ============= IMAGENEN DE PUBLICACION ============= */-->

                <?php


                if (preg_match('/^[0-9]+$/', $post['post_img']) || (empty($post['post_img']))) {

                    // publicacion sin imagen
                ?>


                <?php
                } else {
                ?>
                    <!--// publicacion con imagen  puede ir texto arriba de la imagen -->
                    <img src="assets/images/posts/<?= $post['post_img'] ?>" loading=lazy class="" alt="...">

                <?php
                }
                ?>



                <h4 style="font-size: x-larger" class="p-2 border-bottom d-flex">

                    <!-- /* ============= REACCIONES ============= */-->

                    <!--  PRIMERA REACCION-->

                    <span>
                        <?php
                        $reaction = "heart";
                        if (checkLikeStatus($post['id'], $reaction)) {
                            $like_btn_display = 'none';
                            $unlike_btn_display = '';
                            $reaction = 'heart';
                        } else {
                            $like_btn_display = '';
                            $unlike_btn_display = 'none';
                            $reaction = 'heart';
                        }
                        ?>
                        <i class="bi bi-heart-fill unlike_btn text-danger" style="display:<?= $unlike_btn_display ?>" data-post-id='<?= $post['id'] ?>' data-post-reaction='<?= $reaction ?>'></i>
                        <i class="bi bi-heart like_btn text-danger" style="display:<?= $like_btn_display ?>" data-post-id='<?= $post['id'] ?>' data-post-reaction='<?= $reaction ?>'></i>





                        <!-- SEGUNDA REACCION-->

                        <?php
                        $reaction2 = "five";
                        if (checkLikeStatus($post['id'], $reaction2)) {

                            // el post no tiene esta reaccion
                            $five_btn_display = 'none';
                            $unfive_btn_display = '';
                            $reaction2 = 'five';
                        } else {

                            // tiene reaccion
                            $five_btn_display = '';
                            $unfive_btn_display = 'none';
                            $reaction2 = 'five';
                        }
                        ?>

                        &nbsp;

                        <i class="bi bi-dice-5-fill unfive_btn text-success" style="display:<?= $unfive_btn_display ?>" data-post-id='<?= $post['id'] ?>' data-post-reaction='<?= $reaction2 ?>'></i>
                        <i class="bi bi-dice-5 five_btn text-success" style="display:<?= $five_btn_display ?>" data-post-id='<?= $post['id'] ?>' data-post-reaction='<?= $reaction2 ?>'></i>

                    </span>




                    <?php if ($post['uid'] != $user['id']) { ?>

                        <!-- /* ============= COMPARTIR ============= */-->
                        &nbsp;&nbsp;
                        <span>

                            <!-- <i class="bi bi-share-fill" data-bs-toggle="modal" data-bs-target="#addpost" href="#" id_post="<?= $post['id'] ?>" user_post_from='<?= $post['uid'] ?>' user_now='<?= $user['id'] ?>' picture_user_post_from='<?= $post['profile_pic'] ?>' name='<?= $post['username'] ?>' foto_post= '<?= $post['post_img'] ?>'  text_post='<?= $post['post_text'] ?>'></i> -->
                            <!-- <i class="bi bi-share-fill" data-bs-toggle="modal" data-bs-target="#addshare" href="#" id_post="<?= $post['id'] ?>" user_post_from="<?= $post['uid'] ?>" user_now="<?= $user['id'] ?>" picture_user_post_from="<?= $post['profile_pic'] ?>" name="<?= $post['username'] ?>" foto_post="<?= $post['post_img'] ?>" text_post="<?= $post['post_text'] ?>"></i> -->
                            <i class="bi bi-share-fill" onclick="openModal('<?= $post['id'] ?>', '<?= $post['uid'] ?>', '<?= $user['id'] ?>','<?= $user['first_name'] ?>','<?= $user['last_name'] ?>', 'assets/images/profile/<?= $post['profile_pic'] ?>', '<?= $post['first_name'] ?>','<?= $post['last_name'] ?>', 'assets/images/posts/<?= $post['post_img'] ?>', '<?= $post['post_text'] ?>','<?= $post['post_img'] ?>')"></i>

                        </span>
                    <?php } ?>










                    <!-- <i class="fa-regular fa-strawberry" style="color: #ff0000;"></i> -->


                    <!-- /* =============  ============= */-->


                    &nbsp;&nbsp;<i class="bi bi-chat-left d-flex align-items-center"><span class="p-1 mx-2 text-small" style="font-size:small" data-bs-toggle="modal" data-bs-target="#postview<?= $post['id'] ?>"><?= count($comments) ?> comments</span></i>

                </h4>
                <div>
                    <span class="p-1 mx-2" data-bs-toggle="modal" data-bs-target="#likes<?= $post['id'] ?>"><span id="likecount<?= $post['id'] ?>"><?= count($likes) ?></span> likes</span>
                    <span style="font-size:small" class="text-muted">Posted</span> <?= show_time($post['created_at']) ?>

                </div>


                <!-- altura de tamaño de componente dinamica de acuerdo al numero de comentarios por publicacion-->
                <?php
                if (count($comments) < 1) {
                    $altura = 70;
                ?>
                <?php
                } else if (count($comments) == 1) {
                    $altura = 80;
                } else if (count($comments) > 1) {

                    $altura = 150;
                } ?>








                <div class="flex-fill align-self-stretch overflow-auto" id="comment-section<?= $post['id'] ?>" style="height: <?= $altura ?>px;">

                    <?php
                    if (count($comments) < 1) {
                    ?>
                        <p class="p-2 text-center my-2 nce">no hay comentarios</p>
                    <?php
                    }


                    foreach ($comments as $comment) {
                        $cuser = getUser($comment['user_id']);

                    ?>
                        <div class="d-flex align-items-center p-2">
                            <div><img src="assets/images/profile/<?= $cuser['profile_pic'] ?>" alt="" height="40" width="40" class="rounded-circle border">
                            </div>
                            <div>&nbsp;&nbsp;&nbsp;</div>
                            <div class="d-flex flex-column justify-content-start align-items-start">
                                <h6 style="margin: 0px;"><a href="?u=<?= $cuser['username'] ?>" class="text-decoration-none text-dark text-xm text-muted">@<?= $cuser['username'] ?></a> - <?= $comment['comment'] ?></h6>
                                <p style="margin:0px;" class="text-xm text-muted">(<?= show_time($comment['created_at']) ?>)</p>

                                <!-- /* ============= ELIMINAR COMENTARIO ============= */-->
                                <!-- -->



                                <?php

                                if ($comment['user_id'] == $user['id']) {

                                ?>
                                    <a class="dropdown-item-item small" href="assets/php/actions.php?deletecomment=<?= $post['id'] ?>&comment=<?= $comment['comment'] ?>" onclick="deleteComment(event)"><i class="bi bi-trash-fill"></i> Eliminar</a>

                                <?php

                                }
                                ?>



                            </div>

                        </div>








                    <?php
                    }
                    ?>






                </div>












                <div class="input-group p-2 <?= $post['post_text'] ? 'border-top' : '' ?>">

                    <input type="text" class="form-control rounded-0 border-0 comment-input" placeholder="escribe un comentario..." aria-label="Recipient's username" aria-describedby="button-addon2">
                    <button class="btn btn-outline-primary rounded-0 border-0 add-comment" data-page='wall' data-cs="comment-section<?= $post['id'] ?>" data-post-id="<?= $post['id'] ?>" type="button" id="button-addon2">Post</button>

                </div>

            </div>


            <!-- /* ============= MODAL ============= */-->

            <div class="modal fade" id="postview<?= $post['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body d-md-flex p-0">

                            <?php

                            if (preg_match('/^[0-9]+$/', $post['post_img'])) {

                                // publicacion sin imagen cambia tamaño de modal a todo el texto
                                $m = 'col-md-12';
                                $m2 = $m;
                            ?>
                            <?php
                            } else {
                                $m = 'col-md-8'; // 8 columnas de espacio de la imagen en el modal
                                $m2 = 'col-md-4'; // 4 columnas para el texto
                                // publicacion con imagen cambia tamaño de modal y deja espacio para texto
                            ?>


                                <div class="<?= $m ?> col-sm-12">
                                    <img src="assets/images/posts/<?= $post['post_img'] ?>" style="max-height:90vh" class="w-100 overflow:hidden">
                                </div>

                            <?php
                            }
                            ?>




                            <!-- col-md-4 modificar este valor en el modal por 8 o mas para puro texto-->
                            <div class="<?= $m2 ?> col-sm-12 d-flex flex-column">
                                <div class="d-flex align-items-center p-2 border-bottom">
                                    <div><img src="assets/images/profile/<?= $post['profile_pic'] ?>" alt="" height="50" width="50" class="rounded-circle border">
                                    </div>
                                    <div>&nbsp;&nbsp;&nbsp;</div>
                                    <div class="d-flex flex-column justify-content-start">
                                        <h6 style="margin: 0px;"><?= $post['first_name'] ?> <?= $post['last_name'] ?></h6>
                                        <p style="margin:0px;" class="text-muted">@<?= $post['username'] ?></p>
                                    </div>
                                    <div class="d-flex flex-column align-items-end flex-fill">
                                        <div class=""></div>
                                        <div class="dropdown">
                                            <span class="<?= count($likes) < 1 ? 'disabled' : '' ?>" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                <?= count($likes) ?> likes
                                            </span>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                                <?php
                                                foreach ($likes as $like) {
                                                    $lu = getUser($like['user_id']);
                                                ?>
                                                    <li><a class="dropdown-item" href="?u=<?= $lu['username'] ?>"><?= $lu['first_name'] . ' ' . $lu['last_name'] ?> (@<?= $lu['username'] ?>)</a></li>

                                                <?php
                                                }
                                                ?>

                                            </ul>
                                        </div>
                                        <div style="font-size:small" class="text-muted">Posted <?= show_time($post['created_at']) ?> </div>

                                    </div>
                                </div>


                                <div class="flex-fill align-self-stretch overflow-auto" id="comment-section<?= $post['id'] ?>" style="height: 100px;">

                                    <?php
                                    if (count($comments) < 1) {
                                    ?>
                                        <p class="p-3 text-center my-2 nce">no hay comentarios</p>
                                    <?php
                                    }
                                    foreach ($comments as $comment) {
                                        $cuser = getUser($comment['user_id']);

                                    ?>
                                        <div class="d-flex align-items-center p-2">
                                            <div><img src="assets/images/profile/<?= $cuser['profile_pic'] ?>" alt="" height="40" width="40" class="rounded-circle border">
                                            </div>
                                            <div>&nbsp;&nbsp;&nbsp;</div>
                                            <div class="d-flex flex-column justify-content-start align-items-start">
                                                <h6 style="margin: 0px;"><a href="?u=<?= $cuser['username'] ?>" class="text-decoration-none text-dark text-small text-muted">@<?= $cuser['username'] ?></a> - <?= $comment['comment'] ?></h6>
                                                <p style="margin:0px;" class="text-muted">(<?= show_time($comment['created_at']) ?>)</p>

                                                <!-- /* ============= ELIMINAR COMENTARIO ============= */-->
                                                <!-- -->



                                                <?php

                                                if ($comment['user_id'] == $user['id']) {

                                                ?>
                                                    <a class="dropdown-item" href="assets/php/actions.php?deletecomment=<?= $post['id'] ?>?comment=<?= $comment['comment'] ?>"><i class="bi bi-trash-fill"></i> Eliminar</a>

                                                <?php

                                                }
                                                ?>



                                            </div>

                                        </div>

                                    <?php
                                    }
                                    ?>






                                </div>

                                <div class="input-group p-2 border-top">
                                    <input type="text" class="form-control rounded-0 border-0 comment-input" placeholder="escribe un comentario.." aria-label="Recipient's username" aria-describedby="button-addon2">
                                    <button class="btn btn-outline-primary rounded-0 border-0 add-comment" data-cs="comment-section<?= $post['id'] ?>" data-post-id="<?= $post['id'] ?>" type="button" id="button-addon2">Post</button>
                                </div>
                            </div>



                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="likes<?= $post['id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Likes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <?php
                            if (count($likes) < 1) {
                            ?>
                                <p>Currently No Likes</p>
                            <?php
                            }
                            foreach ($likes as $f) {

                                $fuser = getUser($f['user_id']);
                                $fbtn = '';
                                if (checkBS($f['user_id'])) {
                                    continue;
                                } else if (checkFollowStatus($f['user_id'])) {
                                    $fbtn = '<button class="btn btn-sm btn-danger unfollowbtn" data-user-id=' . $fuser['id'] . ' >Unfollow</button>';
                                } else if ($user['id'] == $f['user_id']) {
                                    $fbtn = '';
                                } else {
                                    $fbtn = '<button class="btn btn-sm btn-primary followbtn" data-user-id=' . $fuser['id'] . ' >Follow</button>';
                                }
                            ?>
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex align-items-center p-2">
                                        <div><img src="assets/images/profile/<?= $fuser['profile_pic'] ?>" alt="" height="40" width="40" class="rounded-circle border">
                                        </div>
                                        <div>&nbsp;&nbsp;</div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <a href='?u=<?= $fuser['username'] ?>' class="text-decoration-none text-dark">
                                                <h6 style="margin: 0px;font-size: small;"><?= $fuser['first_name'] ?> <?= $fuser['last_name'] ?></h6>
                                            </a>
                                            <p style="margin:0px;font-size:small" class="text-muted">@<?= $fuser['username'] ?></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <?= $fbtn ?>

                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>

                    </div>
                </div>
            </div>

        <?php
        }
        ?>



    </div>

    <div class="col-lg-4 col-sm-0 overflow-hidden mt-4 p-sm-0 p-md-3">


        <div class="d-flex align-items-center p-2">
            <div><img src="assets/images/profile/<?= $user['profile_pic'] ?>" alt="" height="60" width="60" class="rounded-circle border">
            </div>
            <div>&nbsp;&nbsp;&nbsp;</div>
            <div class="d-flex flex-column justify-content-center">
                <a href='?u=<?= $user['username'] ?>' class="text-decoration-none text-dark">
                    <h6 style="margin: 0px;"><?= $user['first_name'] ?> <?= $user['last_name'] ?></h6>
                </a>
                <p style="margin:0px;" class="text-muted">@<?= $user['username'] ?></p>
            </div>
        </div>


        <div>
            <h6 class="text-muted p-2">Tambien puedes seguir</h6>
            <?php
            foreach ($follow_suggestions as $suser) {
            ?>
                <div class="d-flex justify-content-between">
                    <div class="d-flex align-items-center p-2">
                        <div><img src="assets/images/profile/<?= $suser['profile_pic'] ?>" alt="" height="40" width="40" class="rounded-circle border">
                        </div>
                        <div>&nbsp;&nbsp;</div>
                        <div class="d-flex flex-column justify-content-center">
                            <a href='?u=<?= $suser['username'] ?>' class="text-decoration-none text-dark">
                                <h6 style="margin: 0px;font-size: small;"><?= $suser['first_name'] ?> <?= $suser['last_name'] ?></h6>
                            </a>
                            <p style="margin:0px;font-size:small" class="text-muted">@<?= $suser['username'] ?></p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-primary followbtn" data-user-id='<?= $suser['id'] ?>'>Follow</button>

                    </div>
                </div>
            <?php
            }

            if (count($follow_suggestions) < 1) {
                echo "<p class='p-2 bg-white border rounded text-center'>No hay sugerencias</p>";
            }
            ?>




        </div>
    </div>
</div>