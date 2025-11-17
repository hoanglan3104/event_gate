<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content login-modal">
      <div class="login-modal-header d-flex align-items-center justify-content-between">
        <h4 class="modal-title mb-0 w-100 text-center">
          <b>Đăng nhập</b>
        </h4>
        <button type="button" class="btn-close btn-close-white login-close" data-bs-dismiss="modal"
                aria-label="Close"></button>
      </div>

      <div class="modal-body login-modal-body">
        <form action="../auth/login.php" method="POST">
          <div class="mb-3">
            <label class="form-label small fw-semibold">Nhập email hoặc số điện thoại</label>
            <div class="input-group login-input-group">
              <span class="input-group-text">
                <i class="bi bi-person-circle"></i>
              </span>
              <input type="email"
                     name="email"
                     class="form-control"
                     placeholder="Nhập email hoặc số điện thoại"
                     required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label small fw-semibold">Nhập mật khẩu</label>
            <div class="input-group login-input-group">
              <span class="input-group-text">
                <i class="bi bi-lock-fill"></i>
              </span>
              <input type="password"
                     name="password"
                     class="form-control login-password-input"
                     placeholder="Nhập mật khẩu"
                     required>
              <button class="input-group-text toggle-password-btn" type="button">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn login-btn-primary w-100 mb-2">
            Tiếp tục
          </button>
        </form>

        <div class="text-center mt-2">
          <a href="#" class="text-muted small" id="openForgotPassword"
             data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">
            Quên mật khẩu?
          </a>
        </div>

        <div class="text-center mt-2 small">
          <span>Chưa có tài khoản?
            <a href="#" class="fw-semibold" id="openRegister">Tạo tài khoản ngay</a>
          </span>
        </div>

        <div class="divider my-3">
          <span>Hoặc</span>
        </div>

        <button class="btn btn-light w-100 login-google-btn" type="button">
          <img src="../assets/images/google-icon.png" alt="Google" width="20" class="me-2">
          Đăng nhập với Google
        </button>

        <p class="text-center text-muted mt-3 mb-0" style="font-size: 12px;">
          Bằng việc tiếp tục, bạn đã đọc và đồng ý với
          <a href="#" class="link-light text-decoration-underline">Điều khoản sử dụng</a>
          và
          <a href="#" class="link-light text-decoration-underline">Chính sách bảo mật thông tin</a>
          của chúng tôi.
        </p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const toggleBtn = document.querySelector("#loginModal .toggle-password-btn");
  const passwordInput = document.querySelector("#loginModal .login-password-input");

  if (toggleBtn && passwordInput) {
    toggleBtn.addEventListener("click", () => {
      const icon = toggleBtn.querySelector("i");
      const isShow = passwordInput.type === "text";
      passwordInput.type = isShow ? "password" : "text";
      icon.classList.toggle("bi-eye");
      icon.classList.toggle("bi-eye-slash");
    });
  }
});
</script>
