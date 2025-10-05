<?php
// ----------- Handle submission -----------
function clean($s){ return htmlspecialchars(trim($s)); }
$submitted = false;
$errors = [];
$result = [];

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $fullname = clean($_POST['fullname'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $mobile = clean($_POST['mobile'] ?? '');
    $dob = clean($_POST['dob'] ?? '');
    $gender = clean($_POST['gender'] ?? '');
    $course = clean($_POST['course'] ?? '');
    $address = nl2br(clean($_POST['address'] ?? ''));

    // Photo upload
    $uploaded_path = '';
    if(!empty($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE){
        $file = $_FILES['photo'];
        $allowed = ['image/jpeg','image/png','image/webp'];
        if($file['error'] === UPLOAD_ERR_OK && in_array($file['type'],$allowed) && $file['size'] <= 2*1024*1024){
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $targetDir = __DIR__ . '/uploads';
            if(!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $safeName = 'photo_'.time().'_'.rand(1000,9999).'.'.$ext;
            $moveTo = $targetDir . '/' . $safeName;
            if(move_uploaded_file($file['tmp_name'], $moveTo)){
                $uploaded_path = 'uploads/' . $safeName;
            }
        }
    }

    // Validation
    if(strlen($fullname) < 3) $errors[] = "Full name seems too short.";
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if(!preg_match('/^[0-9]{7,15}$/', $mobile)) $errors[] = "Invalid mobile number.";

    if(!$errors){
        $submitted = true;
        $result = compact('fullname','email','mobile','dob','gender','course','address','uploaded_path');
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Online Application Form</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
:root{
  --glass-bg: rgba(255,255,255,0.15);
  --accent1: #6EE7B7;
  --accent2: #3B82F6;
}
*{box-sizing:border-box;font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;}
body{
  margin:0;
  min-height:100vh;
  background:linear-gradient(135deg,var(--accent1),var(--accent2));
  color:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  flex-direction:column;
  padding:40px;
}
.container{max-width:1100px;width:100%;display:grid;grid-template-columns:1fr 380px;gap:24px;}
.card{background:var(--glass-bg);border-radius:18px;padding:24px;backdrop-filter: blur(12px);box-shadow:0 8px 30px rgba(2,6,23,0.35);}
h1{margin:0;font-size:28px;}
label{font-size:13px;margin-bottom:6px;opacity:0.95;color:#fff;}
input,select,textarea{
  width:100%;
  background:rgba(255,255,255,0.2);
  border:1px solid rgba(255,255,255,0.25);
  padding:10px 12px;
  border-radius:10px;
  color:#fff;
  font-size:15px;
  outline:none;
  transition:0.3s;
}
input::placeholder, textarea::placeholder{
  color:rgba(255,255,255,0.7);
}

/* 👇 Fix for dropdown visibility */
select {
  background-color: rgba(255,255,255,0.2);
  color: #fff;
  appearance: none;
}
select option {
  background-color: #0d1b2a;  /* dark background */
  color: #fff;               /* visible white text */
}

/* highlight on focus */
input:focus,textarea:focus,select:focus{
  border-color:#fff;
  background:rgba(255,255,255,0.3);
  box-shadow:0 0 10px rgba(255,255,255,0.3);
}

.row{display:flex;gap:16px;margin-top:12px;}
.col{flex:1;display:flex;flex-direction:column;}
.btn{
  background:linear-gradient(90deg,var(--accent2),var(--accent1));
  border:none;
  padding:10px 16px;
  border-radius:12px;
  color:#062;
  font-weight:600;
  cursor:pointer;
  transition:.2s;
}
.btn:hover{
  transform:translateY(-3px);
  box-shadow:0 8px 30px rgba(2,6,23,0.35);
}
.btn-ghost{
  background:transparent;
  border:1px solid rgba(255,255,255,0.4);
  color:#fff;
}
.form-message{margin-top:10px;color:#ffe5e5;}
.preview-card{margin-top:16px;}
.preview{
  min-height:220px;
  background:rgba(255,255,255,0.08);
  padding:14px;
  border-radius:12px;
  color:#fff;
}
.result-card{
  max-width:900px;
  margin:40px auto;
  padding:26px;
  border-radius:14px;
  background:rgba(255,255,255,0.1);
  color:#fff;
  box-shadow:0 14px 40px rgba(3,10,36,0.5);
}
.result-top{display:flex;gap:18px;align-items:center;}
.result-top img{
  width:120px;
  height:120px;
  object-fit:cover;
  border-radius:12px;
  border:1px solid rgba(255,255,255,0.3);
}
@media(max-width:980px){.container{grid-template-columns:1fr;}}
</style>
</head>
<body>

<?php if($submitted): ?>
  <div class="result-card">
    <div class="result-top">
      <?php if($result['uploaded_path']): ?>
        <img src="<?= $result['uploaded_path'] ?>" alt="Profile Photo">
      <?php else: ?>
        <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='120'><rect width='100%' height='100%' fill='%23000'/><text x='50%' y='50%' fill='%23fff' font-size='14' font-family='Arial' dominant-baseline='middle' text-anchor='middle'>No Photo</text></svg>">
      <?php endif; ?>
      <div>
        <h2><?= $result['fullname'] ?></h2>
        <div><strong>Email:</strong> <?= $result['email'] ?></div>
        <div><strong>Mobile:</strong> <?= $result['mobile'] ?></div>
        <div><strong>DOB:</strong> <?= $result['dob'] ?></div>
        <div><strong>Gender:</strong> <?= $result['gender'] ?></div>
        <div><strong>Course:</strong> <?= $result['course'] ?></div>
      </div>
    </div>
    <div style="margin-top:18px;">
      <h3>Address</h3>
      <div style="background:rgba(255,255,255,0.04);padding:12px;border-radius:8px;"><?= $result['address'] ?: '—'; ?></div>
    </div>
    <div style="margin-top:20px;">
      <a href="index.php" class="btn">Submit Another Application</a>
    </div>
  </div>
<?php else: ?>
  <div class="container">
    <div class="card">
      <h1>Apply Now</h1>
      <p>Elegant online application — quick & beautiful</p>

      <?php if($errors): ?>
        <div class="form-message"><?= implode('<br>', $errors) ?></div>
      <?php endif; ?>

      <form id="regForm" method="post" enctype="multipart/form-data">
        <div class="row">
          <div class="col">
            <label>Full Name</label>
            <input type="text" name="fullname" id="fullname" required>
          </div>
          <div class="col">
            <label>Email</label>
            <input type="email" name="email" id="email" required>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <label>Mobile Number</label>
            <input type="tel" name="mobile" id="mobile" required>
          </div>
          <div class="col">
            <label>Date of Birth</label>
            <input type="date" name="dob" id="dob" required>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <label>Gender</label>
            <div>
              <label><input type="radio" name="gender" value="Male" checked> Male</label>
              <label><input type="radio" name="gender" value="Female"> Female</label>
              <label><input type="radio" name="gender" value="Other"> Other</label>
            </div>
          </div>
          <div class="col">
            <label>Course / Program</label>
            <select name="course" id="course" required>
              <option value="">-- Select --</option>
              <option>B.E</option>
              <option>B.Tech</option>
              <option>BCA</option>
              <option>B.Sc Computer Science</option>
              <option>MCA</option>
              <option>MBA</option>
              <option>B.Com</option>
              <option>BBA</option>
            </select>
          </div>
        </div>

        <label>Address</label>
        <textarea name="address" id="address" rows="3" required></textarea>

        <label>Profile Photo (optional)</label>
        <input type="file" name="photo" accept="image/*">

        <div class="row" style="margin-top:16px;">
          <button type="submit" class="btn">Submit Application</button>
          <button type="reset" class="btn btn-ghost">Reset</button>
        </div>
      </form>
    </div>

    <div class="card preview-card">
      <h3>Live Preview</h3>
      <div id="livePreview" class="preview">
        <p class="muted">Fill in the form to see a styled preview here before submitting.</p>
      </div>
    </div>
  </div>

  <footer style="margin-top:20px;opacity:0.9;"></footer>

  <script>
  $(function(){
    function updatePreview(){
      const name=$('#fullname').val();
      const email=$('#email').val();
      const mobile=$('#mobile').val();
      const course=$('#course').val();
      const address=$('#address').val();
      const dob=$('#dob').val();
      const gender=$('input[name=gender]:checked').val();
      if(!name&&!email){$('#livePreview').html('<p class=\"muted\">Fill in the form to see a styled preview here before submitting.</p>');return;}
      $('#livePreview').html(`
        <div><strong>${name}</strong><br>${email}</div>
        <div>${mobile? '📱 '+mobile:''}</div>
        <div>${course? '<strong>Course:</strong> '+course:''}</div>
        <div>${dob? '<strong>DOB:</strong> '+dob:''}</div>
        <div>${gender? '<strong>Gender:</strong> '+gender:''}</div>
        <div>${address? '<strong>Address:</strong> '+address:''}</div>
      `);
    }
    $('#regForm input, #regForm textarea, #regForm select').on('input change',updatePreview);
  });
  </script>
<?php endif; ?>

</body>
</html>
