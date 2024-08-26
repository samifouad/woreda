<style>
  /*
    html {
            -webkit-text-size-adjust: none;
            touch-action: pan-y; // prevent user scaling
        }
        
    //  * {
    //    box-sizing: border-box;
    //    margin: 0;
    //    padding: 0;
   //   }

   */

      .itemContext {
        position: relative;
        width: 100%;
        height: 20%;
        background-color: rgba(0,0,0,0.35);
        margin-top: -31%;
        border-radius: 5px;
      }

      .itemContent {
        display: none;
      }

      .backgroundObfuscate {
        display: none;
        width: 100%;
        height: 100vh;
        z-index: 40;
        position: absolute;
        top: 0px;
        left: 0px;
        background-color: rgba(0,0,0,0.9);
        /* backdrop-filter: blur(5px); */
        opacity: 1;
      }

      @media only screen and (max-width: 480px) {
        .wrapper {
          width: 96%;
          margin: 0 auto;
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          grid-auto-rows: 150px;
          grid-gap: 5px;
          grid-auto-flow: dense;
        }
        .item {
          background: #58d5f6;
          color: white;
          padding: 0px;
          border-radius: 5px;
        }
        .item:nth-child(even) {
          background: #236fc8;
        }
        .hero {
          grid-column: span 2;
          grid-row: span 2;
        }
        .popular {
          grid-column: span 2;
          grid-row: span 2;
        }
        .interesting {
          grid-column: span 2;
          grid-row: span 1;
        }
        .vert {
          grid-column: span 1;
          grid-row: span 2;
        }
      }
      @media only screen and (min-width: 481px) {
        .wrapper {
          width: 96%;
          margin: 0 auto;
          display: grid;
          grid-template-columns: repeat( auto-fit, minmax(200px, 3fr) );
          grid-auto-rows: 150px;
          grid-gap: 5px;
          grid-auto-flow: dense;
        }
        .item {
          background: #58d5f6;
          color: white;
          padding: 0px;
          border-radius: 5px;
        }
        .item:nth-child(even) {
          background: #236fc8;
        }
        .hero {
          grid-column: span 2;
          grid-row: span 2;
        }
        .popular {
          grid-column: span 2;
          grid-row: span 2;
        }
        .interesting {
          grid-column: span 2;
          grid-row: span 1;
        }
        .vert {
          grid-column: span 1;
          grid-row: span 2;
        }
      }
      @media only screen and (min-width: 630px) {
        .wrapper {
          width: 96%;
          margin: 0 auto;
          display: grid;
          grid-template-columns: repeat( auto-fit, minmax(200px, 3fr) );
          grid-auto-rows: 150px;
          grid-gap: 5px;
          grid-auto-flow: dense;
        }
        .item {
          background: #58d5f6;
          color: white;
          padding: 0px;
          text-align: center;
          border-radius: 5px;
        }
        .item:nth-child(even) {
          background: #236fc8;
        }
        .item:hover {
          cursor: pointer;
        }
        .hero {
          grid-column: span 2;
          grid-row: span 2;
        }
        .popular {
          grid-column: span 2;
          grid-row: span 2;
        }
        .interesting {
          grid-column: span 2;
          grid-row: span 1;
        }
        .vert {
          grid-column: span 1;
          grid-row: span 2;
        }
      }
      .top {
        width: 100%;
        height: 90px;
        position: relative;
        z-index: 1;
      }
      .categories {
        width: 240px;
        float: left;
        padding-left: 5%;
        margin-top: 10px;
        margin-bottom: 10px;
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;
        z-index: 1;
      }
      .categories::-webkit-scrollbar {
          display: none;
        }
      .cat-title {
        height: 80px;
        line-height: 80px;
        font-size: 28px;
        color: #666;
        margin-right: 15px;
        float: left;
      }
      .cat-title:hover {
        cursor: pointer;
      }
      .cat-active {
        color: #000;
        font-weight: bold;
        font-size: 36px;
      }
      .cat-fade {
        /* background: rgba(251,251,251,0.95); */
        background-image: linear-gradient(to right, rgba(246,236,226,0.90), rgba(246,236,226,1));
        position: absolute;
        width: 80px;
        margin-top: 25px;
        height: 50px;
        left: 240px;
        z-index: 3;
        float: left;
        border-left: 1px solid rgba(225,225,225,0.65);
      }
      .cat-fade-left {
        position: absolute;
        width: 80px;
        left: 0px;
        float: left;
        margin-top: 25px;
        height: 50px;
        z-index: 3;
        border-right: 1px solid rgba(225,225,225,0.65);
        background-image: linear-gradient(to right, rgba(246,236,226,0.9), rgba(246,236,226,1));
      }
      .filter {
        float: right;
        width: 25%;
        max-width: 90px;
        background: #dfdfdf;
        border-radius: 10px;
        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 0px;
        padding-right: 0px;
        margin-top: 30px;
        margin-right: 5%;
        text-align: center;
        font-weight: bold;
      }
      .filter:hover {
        cursor: pointer;
      }
    </style>
    <script>
      var cardIsOpen = false;
      var cardIsOpenWhen = "";
      var cardCurrent = "";
      var cardCurrentScroll = "";
      var cardOriginal = "";
      var cardNewView = "";

      $(document).ready(function()
      {
        /*
        $(".wrapper").swipe({
          swipeLeft:function(event, direction, distance, duration, fingerCount){
            alert("You swiped " + direction + " for " + distance + "px" );
          },
          threshold:50
        });
        $(".wrapper").swipe({
          swipeRight:function(event, direction, distance, duration, fingerCount){
            alert("You swiped " + direction + " for " + distance + "px" );
          },
          threshold:50
        });
        */

        function bgBlurStart ()
        {
          $('.backgroundObfuscate').fadeIn("fast");
          var stv = $(window).scrollTop();
          $('.backgroundObfuscate').css("top", stv);
        }

        function bgBlurEnd ()
        {
          $('.backgroundObfuscate').fadeOut("fast");
        }

        function cardDoOpen (newView)
        {
          cardCurrentScroll = $(window).scrollTop();
          $(newView).transition({ 
                  top: '0', 
                  left: '0', 
                  height: 'calc(100vh - 90px)', 
                  width: '100%', 
                  duration: 150,
                  queue: false
              });

          $(newView).children('img').transition({ 
              height: '250px', 
              width: '500px', 
              duration: 150,
              queue: false
          });
          $(newView).children('.itemContent').fadeIn("slow");
          $("body").css("height", "100%");
          $("body").css("overflow", "hidden");
          //$("body").css("position", "fixed");
          //bgBlurStart();
          var d = new Date();
          cardIsOpenWhen = d.getTime();
        }

        function cardDoClose ()
        {
          var nd = new Date();
          cardCurrent = nd.getTime();
          if (cardCurrent > cardIsOpenWhen+5)
          {
            //$('.backgroundObfuscate').css("display", "none");
            $(cardOriginal).fadeIn("fast", function(){
              $(cardNewView).fadeOut("fast");
              //$("body").css("position", "relative");
              //$(window).scrollTop(cardCurrentScroll);
              cardIsOpen = false;
              cardIsOpenWhen = "";
              cardCurrent = "";
              cardOriginal = "";
              cardCurrentScroll = "";
              $("body").css("height", "100%");
              $("body").css("overflow", "auto");
            });
            $(cardNewView).remove();
            cardNewView = "";
          }
        }

        // close open dialog when clicks outside of it occur
        $(window).mouseup(function(e)
        {
          if (cardIsOpen == true) {
            if (e.target !== cardNewView)
            {
              cardDoClose();
            } else {
              console.log("click in view");
            }
          } else {
              console.log("other click");
          }
        });

        $(document).on("touchmove", function(e)
        { 
          if (cardIsOpen == true) {
           // e.preventDefault();
          }
        });

        // close open dialog when touch outside of it occurs
        $('html, body').on("touchstart touchmove", function(e)
        {
          if (cardIsOpen == true) {
            if (e.target !== cardNewView)
            {
              cardDoClose();
            } else {
              console.log("click in view");
            }
          } else {
              console.log("other click");
          }
        });

        // close open dialog when clicks outside of it occur
        $(".item").mouseup(function(e)
        {
          if (cardIsOpen == false)
          {
            // avoid activating click animation for children
            // used in combination with CSS 'pointer-events: none;'
            if (e.target !== this)
            {
                // converts click on child image to
                // a click on parent item
                e.target = this.closest('.item');
            }

            // ID the card
            const card = e.target;
            cardOriginal = e.target;
            cardIsOpen = true;

            // clone the card
            const cardClone = card.cloneNode(true);
            
            // get the location of the card in the view
            const {top, left, width, height} = card.getBoundingClientRect();

            // position the clone on top of the original
            cardClone.style.position = 'absolute';
            cardClone.style.top = top + 'px';
            cardClone.style.left = left + 'px';
            cardClone.style.width = width + 'px';
            cardClone.style.height = height + 'px';
            $(cardClone).css("z-index", "50");

            // hide the original card with opacity
            card.style.display = 'none';

            // add card to body & avoid parent container
            $("body").append(cardClone);
            cardNewView = cardClone;

            cardDoOpen(cardClone);
          }
        });
      });
    </script>
  </head>
  <body>
    <div class="backgroundObfuscate"></div>
    <div class="top">
      <div class="cat-fade-left"></div>
      <div class="categories">
        <div style="width: 982px;">
        <div class="cat-title cat-active" id="cat-Today" style="margin-left: 90px;">Today</div>
        <div class="cat-title" id="cat-Yesterday">Yesterday</div>
        <div class="cat-title" id="cat-LastSunday">Sunday</div>
        <div class="cat-title" id="cat-LastSaturday">Saturday</div>
        <div class="cat-title" id="cat-LastFriday">Friday</div>
        <div class="cat-title" id="cat-LastThursday">Thursday</div>
        <div class="cat-title" id="cat-LastWednesday">Wednesday</div>
        </div>
      </div>
      <div class="filter">Filter</div>
      <div class="cat-fade"></div>
    </div>
    <div class="wrapper">
      <div class="item vert"><img src="/assets/images/grid/6.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
        <div class="itemContext">context</div>
        <div class="itemContent">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent eget leo in dui consequat imperdiet sit amet in tortor. Praesent vel lacus iaculis mauris vulputate maximus ac vel purus. Suspendisse consectetur dui in quam sodales elementum. Curabitur hendrerit dui venenatis erat condimentum, nec interdum urna venenatis. Aenean congue augue sodales dictum cursus. In sed est a ligula laoreet cursus luctus sed tortor. Morbi rutrum interdum tempus.
          <br><br>
          Aenean non sapien ullamcorper, ullamcorper dolor eu, aliquet sapien. Curabitur metus quam, bibendum at nulla at, facilisis accumsan turpis. Vestibulum mattis cursus ex, quis luctus massa viverra fermentum. Aenean orci leo, finibus sed laoreet lobortis, ultricies nec mauris. Praesent diam sapien, accumsan eget tortor vitae, bibendum ornare lacus. Donec mollis, ipsum a fermentum tristique, risus nulla maximus dolor, non gravida mauris purus a sem. Quisque luctus erat vel justo condimentum tempor.
          <br><br>
          Ut cursus finibus nisl, vitae finibus sapien laoreet id. Nulla facilisi. Mauris non vulputate nulla, in maximus dui. Nulla id nunc sit amet quam auctor sagittis. Vestibulum quam lorem, varius in malesuada a, placerat id ante. Proin tristique tellus faucibus, efficitur nisl at, sodales lectus. Integer in varius urna, in tincidunt libero. Maecenas sodales vehicula lacus, nec rhoncus quam molestie sed. Praesent pharetra posuere quam, sit amet facilisis purus feugiat nec. Nulla auctor dui vitae est gravida tincidunt. Sed venenatis, metus id maximus convallis, purus ex blandit quam, sed pulvinar dui diam ut libero. Curabitur nec varius enim. Ut rutrum finibus dui, sit amet iaculis tortor. Vivamus egestas eu felis id posuere. Sed aliquet quam et diam sollicitudin feugiat. Mauris lacinia neque vel neque pretium, et vehicula ipsum placerat.
          <br><br>
          Aenean mauris magna, fermentum in enim non, pellentesque imperdiet risus. Donec vitae euismod odio, at finibus justo. Nunc pharetra ultrices lectus, eu egestas risus posuere id. Cras eleifend dignissim condimentum. Aenean eget euismod mauris. Sed tempor nisi ac leo iaculis, sit amet commodo sem malesuada. Nulla lectus ligula, varius et condimentum vitae, laoreet ac diam. Maecenas a augue vel sem vehicula luctus. Donec fringilla quis urna quis tempor.
          <br><br>
          Vivamus eleifend facilisis lacus, ullamcorper ullamcorper lectus posuere id. Sed congue congue ligula, et lacinia lacus molestie id. Aenean ligula turpis, tristique gravida lorem quis, scelerisque lobortis quam. Proin iaculis erat in volutpat tincidunt. Curabitur dapibus non arcu id pellentesque. Cras vel tellus justo. Morbi ante dolor, cursus ultrices ipsum vel, accumsan convallis sapien. Vestibulum imperdiet maximus ex id euismod.
          <br><br>
          Sed rhoncus scelerisque arcu, id aliquet mauris ullamcorper at. Nunc rhoncus scelerisque faucibus. Vivamus consectetur diam sed nunc eleifend, eget egestas diam scelerisque. Sed id ante egestas, pharetra dolor vitae, rhoncus quam. Suspendisse convallis leo a ligula porta, nec gravida nisi elementum. Curabitur auctor enim in diam finibus, accumsan posuere lacus auctor. Suspendisse potenti. Aliquam venenatis lectus id lorem eleifend dapibus. Aliquam a aliquet augue. Phasellus nec sagittis libero, et volutpat urna. Quisque urna mauris, luctus a nulla ultricies, facilisis pellentesque lacus. Pellentesque eget pretium libero, a accumsan mauris. Integer lobortis neque a dui pulvinar, in dictum quam tempus. Praesent lectus tellus, varius non auctor vel, fringilla ut tortor. Morbi pellentesque tincidunt dui eu egestas.
          <br><br>
          Nam eu bibendum massa. Sed feugiat non lorem eget tristique. Donec faucibus, mi sed accumsan commodo, lorem mauris sodales enim, quis facilisis nibh mauris quis augue. In a diam in libero congue bibendum efficitur sed leo. Quisque euismod, mi auctor molestie accumsan, eros augue congue urna, ut facilisis elit dolor nec elit. Phasellus gravida cursus felis et rhoncus. Cras dictum tristique augue, at mollis lacus commodo non. Phasellus fringilla, velit vitae viverra vehicula, sem massa interdum tortor, ut pellentesque justo justo at metus. Duis eget enim neque. Donec sapien odio, lacinia vitae leo ut, vestibulum semper eros. Phasellus eu nunc vitae nisl iaculis consequat a vel mi.
          <br><br>
          Aenean vehicula porttitor luctus. Praesent vitae massa vel turpis maximus aliquam a quis augue. Fusce vitae vulputate purus. In egestas luctus venenatis. Donec laoreet massa ac leo tempor, ac fermentum quam fringilla. Quisque luctus erat a sem efficitur malesuada. Sed aliquam elementum nibh, eu fringilla nunc suscipit quis. Integer at massa feugiat lectus vulputate vestibulum vel sit amet arcu. Aenean molestie eros a egestas iaculis.
          <br><br>
          Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam id leo ut purus tincidunt pretium nec ut libero. Suspendisse potenti. Fusce in massa quis nibh posuere dignissim vel dictum urna. Nunc nec est ultricies, placerat dui quis, interdum tortor. Nam nec libero sit amet risus placerat facilisis eu ac sapien. Nullam nulla risus, feugiat vitae commodo ut, auctor quis velit.
          <br><br>
          Aliquam quis neque posuere, lobortis lorem ut, sodales felis. Vestibulum finibus, libero quis rutrum scelerisque, ipsum elit finibus quam, vel lobortis purus libero non lacus. Aenean ut odio diam. Nullam ac nunc nisl. Nulla condimentum aliquet dui et vestibulum. Aenean laoreet dignissim purus ac bibendum. In blandit, velit quis rutrum consectetur, velit velit rutrum arcu, in gravida neque velit vitae erat. Aliquam ultricies ante vel neque aliquet rutrum. Aenean consectetur, libero nec ultrices pulvinar, enim quam dignissim arcu, sed sollicitudin magna nisi vitae eros. Vivamus elementum, metus in consequat ornare, arcu enim fermentum lacus, a egestas dolor tortor eu nisi. Pellentesque nulla justo, commodo vel ante pretium, sollicitudin egestas ex.
        </div>
      </div>
      <div class="item">
        <img src="/assets/images/grid/9.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
      </div>
      <div class="item vert">
        <img src="/assets/images/grid/2.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
      </div>
      <div class="item">
        <img src="/assets/images/grid/3.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
      </div>
      <div class="item">
        <img src="/assets/images/grid/4.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
      </div>
      <div class="item">
        <img src="/assets/images/grid/5.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
      </div>
      <div class="item hero">
        <img src="/assets/images/grid/1.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
      </div>
      <div class="item">
        <img src="/assets/images/grid/7.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
      </div>
      <div class="item">
        <img src="/assets/images/grid/8.jpg" style="width:100%; height: 100%; object-fit: cover; overflow: hidden; pointer-events: none; border-radius: 3px;">
      </div>
      <div class="item"></div>
      <div class="item"></div>
      <div class="item vert"></div>
      <div class="item"></div>
      <div class="item"></div>
      <div class="item interesting"></div>
      <div class="item"></div>
      <div class="item"></div>
      <div class="item"></div>
      <div class="item"></div>
      <div class="item hero"></div>
      <div class="item"></div>
      <div class="item"></div>
      <div class="item interesting"></div>
      <div class="item"></div>
    </div>