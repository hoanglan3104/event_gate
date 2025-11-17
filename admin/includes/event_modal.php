<!-- Modal Sửa sự kiện -->
<div class="modal fade" id="editEventModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header">
        <div class="d-flex flex-column">
          <h5 class="modal-title d-flex align-items-center gap-2">
            <i class="bi bi-easel2"></i>
            <span>Sự kiện &amp; Phim</span>
          </h5>
          <span class="small text-muted">
            Mã sự kiện: <span id="eventIdDisplay" class="fw-semibold"></span>
          </span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <form action="update_event.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="eventId" name="event_id">
          <input type="hidden" id="oldEventImg" name="old_event_img">

          <div class="mb-3">
            <label for="location" class="form-label">Địa điểm</label>
            <input type="text" class="form-control" id="location" name="location" required>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-2">
                <label for="eventName" class="form-label">Tên sự kiện &amp; Phim</label>
                <input type="text" class="form-control" id="eventName" name="event_name" required>
              </div>

              <div class="mb-2">
                <label for="startTime" class="form-label">Thời gian bắt đầu</label>
                <input type="datetime-local" class="form-control" id="startTime" name="start_time" required>
              </div>

              <div class="mb-2">
                <label for="price" class="form-label">Giá vé (VND)</label>
                <input type="number" class="form-control" id="price" name="price" min="0" required>
              </div>

              <div class="mb-2">
                <label for="duration" class="form-label">Thời lượng (giờ)</label>
                <input type="number" class="form-control" id="duration" name="duration" min="1" required>
              </div>

              <div class="mb-2">
                <label for="totalSeats" class="form-label">Tổng số ghế</label>
                <p id="seatWarning" class="text-danger mt-1" style="display:none;">
                  Không thể thay đổi số lượng ghế vì sự kiện đã có người đặt.
                </p>
                <select class="form-select" id="totalSeats" name="total_seats">
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="eStatus" class="form-label">Trạng thái</label>
                <select class="form-select" id="eStatus" name="eStatus">
                  <option>Chưa diễn ra</option>
                  <option>Đang diễn ra</option>
                  <option>Đã kết thúc</option>
                  <option>Đã bị hủy</option>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="text-center mb-2">
                <label class="form-label">Ảnh sự kiện</label>
                <img id="eventImagePreview"
                     src=""
                     alt="Ảnh sự kiện"
                     class="img-fluid rounded shadow mb-2"
                     style="height:auto;width:100%;object-fit:cover;">
                <input type="file" class="form-control mb-2" id="eventImageInput" name="event_img">
              </div>

              <div class="mb-2">
                <label for="eventImageLink" class="form-label">Hoặc dán link ảnh</label>
                <input type="text" class="form-control" id="eventImageLink" name="event_img_link">
              </div>

              <div class="mb-2">
                <label for="eventKind" class="form-label">Loại nội dung</label>
                <select class="form-select" id="eventKind" name="event_kind">
                  <option value="event">Sự kiện</option>
                  <option value="movie">Phim</option>
                </select>
              </div>

              <div class="mb-2">
                <label for="eventType" class="form-label">Thể loại</label>
                <select class="form-select" id="eventType" name="event_type">
                  <option value="music">Âm nhạc</option>
                  <option value="art">Nghệ thuật</option>
                  <option value="visit">Tham quan</option>
                  <option value="tournament">Giải đấu</option>
                  <option value="movie">Phim</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer d-flex justify-content-between align-items-center">
          <div id="seats-warning" class="text-danger fw-semibold"></div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
