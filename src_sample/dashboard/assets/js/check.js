
function loadLottieAnimations() {
  const animations = [
    {
      containerId: "confetti-animation",
      path: "api/user-animation.json",
    },
    {
      containerId: "confetti-animation1",
      path: "api/user-animation1.json",
    },
  ];

  animations.forEach((animation) => {
    lottie.loadAnimation({
      container: document.getElementById(animation.containerId),
      renderer: "svg",
      loop: true,
      background: "transparent",
      autoplay: true,
      path: animation.path,
    });
  });
}

loadLottieAnimations();

if (LOGIN_PAGE) {
  function checkLoginForm() {
    var username = $("#username").val();

    var password = $("#password").val();

    if (username && password) {
      $(".btn-login").attr("disabled", false);
    } else {
      $(".btn-login").attr("disabled", true);
    }
  }

  $("#username, #password").on("input", function () {
    checkLoginForm();
  });

  $(document).ready(function () {
    $(".btn-login").attr("disabled", true);
  });

  $("#username, #password").keypress(function (event) {
    if (event.keyCode == 13 || event.which == 13) login();
  });

  $(".btn-login").click(function () {
    login();
  });

  $(".show-password").click(function () {
    if ($("#password").val()) {
      if ($(this).hasClass("active")) {
        $(this).removeClass("active");

        $("#password").attr("type", "password");
      } else {
        $(this).addClass("active");

        $("#password").attr("type", "text");
      }

      $(this).find("span").toggleClass("fas fa-eye fas fa-eye-slash");
    }
  });
}

function generateCaptcha(action, id) {
  if (RECAPTCHA_ACTIVE && action && id && $("#" + id).length) {
    grecaptcha
      .execute(RECAPTCHA_SITEKEY, { action: action })
      .then(function (token) {
        var recaptchaResponse = document.getElementById(id);
        recaptchaResponse.value = token;
      });
  }
}

grecaptcha.ready(function () {
  generateCaptcha("login", "recaptcha_response");
});



function login() {
  var username = $("#username").val();
  var password = $("#password").val();
  var csrf_token = $("#csrf_token").val();


  grecaptcha.execute(RECAPTCHA_SITEKEY, { action: "login" }).then(function (token) {
    var recaptchaResponse = token;

    if (
      $(".alert-login").hasClass("alert-danger") ||
      $(".alert-login").hasClass("alert-success")
    ) {
      $(".alert-login").removeClass("alert-danger alert-success");
      $(".alert-login").addClass("d-none").html("");
    }

    if ($(".show-password").hasClass("active")) {
      $(".show-password").removeClass("active");
      $("#password").attr("type", "password");
      $(".show-password").find("span").toggleClass("fas fa-eye fas fa-eye-slash");
    }
    $(".show-password").addClass("disabled");
    $(".btn-login .sk-chase").removeClass("d-none");
    $(".btn-login span").addClass("d-none");
    $(".btn-login").attr("disabled", true);
    $("#username").attr("disabled", true);
    $("#password").attr("disabled", true);
    $.ajax({
      type: "POST",
      dataType: "json",
      url: "api/login.php",
      async: false,
      data: {
        username: username,
        password: password,
        csrf_token: csrf_token,
        recaptcha_response: recaptchaResponse,
      },
      success: function (result) {
        if (result.success) {
          window.location = "index.php";
        } else if (result.error) {
          $(".alert-login").removeClass("d-none");
          $(".show-password").removeClass("disabled");
          $(".btn-login .sk-chase").addClass("d-none");
          $(".btn-login span").removeClass("d-none");
          $(".btn-login").attr("disabled", false);
          $("#username").attr("disabled", false);
          $("#password").attr("disabled", false);
          $(".alert-login").removeClass("alert-success").addClass("alert-danger");
          $(".alert-login").html(result.error);
        }
      },
    });
  });
}
