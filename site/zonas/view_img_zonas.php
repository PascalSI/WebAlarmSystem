<div class="modal fade" id="myModalViewImgZona"  role="basic" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Imagenes <?php echo $modSingular;?> <span id="title-img-zona"></span></h4>
            </div>
            <div class="modal-body">
                <div id="myCarousel" class="carousel slide">
                  <ol class="carousel-indicators"  id="car-img-zonas-indicators">
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                  </ol>
                  <!-- Carousel items -->
                  <div class="carousel-inner" id="car-img-zonas-content">
                        <div class="item active">
                            <img src="http://placehold.it/640x480/FFF/" alt="" />
                            <div class="carousel-caption">
                                <p>Template.</p>
                            </div>
                        </div>
                  </div>
                  <!-- Carousel nav -->
                  <a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
                  <a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn bg-red"  data-dismiss="modal" >
                    Salir
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->