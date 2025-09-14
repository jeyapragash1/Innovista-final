<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Give Feedback</title>
  <!-- Link to stylesheet -->
  <link rel="stylesheet" href="../public/assets/css/feedback_form.css">
</head>
<body>

<div class="feedback-card">
  <h3>Give Feedback</h3>

  <form method="POST" action="../handlers/feedback_handler.php">
    <!-- Booking ID from query -->
    <input type="hidden" name="booking_id" value="<?php echo $_GET['booking_id']; ?>">

    <!-- ⭐ Star Rating -->
    <div class="mb-3">
      <label class="form-label">Rating</label>
      <div class="star-rating">
        <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
        <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
        <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
        <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
        <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
      </div>
    </div>

    <!-- 💬 Comment -->
    <div class="mb-3">
      <label class="form-label">Comment</label>
      <textarea name="comment" rows="4" placeholder="Write your experience..."></textarea>
    </div>

    <!-- ✅ Buttons -->
    <div class="d-flex">
      <a href="my_projects.php" class="btn btn-secondary">Back</a>
      <button type="submit">Submit Feedback</button>
    </div>
  </form>
</div>

</body>
</html>

<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Give Feedback</title>
  <!-- Link to stylesheet -->
  <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>

<div class="feedback-card">
  <h3>Give Feedback</h3>

  <form method="POST" action="../handlers/feedback_handler.php">
    <!-- Booking ID from query -->
    <input type="hidden" name="booking_id" value="<?php echo $_GET['booking_id']; ?>">

    <!-- ⭐ Star Rating -->
    <div class="mb-3">
      <label class="form-label">Rating</label>
      <div class="star-rating">
        <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
        <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
        <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
        <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
        <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
      </div>
    </div>

    <!-- 💬 Comment -->
    <div class="mb-3">
      <label class="form-label">Comment</label>
      <textarea name="comment" rows="4" placeholder="Write your experience..."></textarea>
    </div>

    <!-- ✅ Buttons -->
    <div class="d-flex">
      <a href="my_projects.php" class="btn btn-secondary">Back</a>
      <button type="submit">Submit Feedback</button>
    </div>
  </form>
</div>

</body>
</html>
