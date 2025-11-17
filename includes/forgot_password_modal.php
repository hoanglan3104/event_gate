<div class="modal fade" id="forgotPasswordModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content forgot-modal">
      <div class="modal-header text-center position-relative forgot-header">
        <h4 class="modal-title w-100"><b>Quên mật khẩu</b></h4>
        <span class="close-icon" data-bs-dismiss="modal" aria-label="Close">
          <i class="bi bi-x-circle"></i>
        </span>
      </div>

      <div class="modal-body forgot-body">
        <p class="forgot-text">
          Nhập email đã đăng ký để nhận liên kết đặt lại mật khẩu.
        </p>
        <form action="../auth/send_reset_link.php" method="POST">
          <div class="mb-3">
            <label class="form-label">Email của bạn</label>
            <input type="email" name="email" class="form-control forgot-input" placeholder="name@example.com" required>
          </div>
          <button type="submit" class="btn w-100 forgot-btn">
            Gửi liên kết
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
