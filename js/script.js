// تحديد شريط التنقل
let navbar = document.querySelector(".header .flex .navbar");

// تحديد واجهة المستخدم الشخصية
let profile = document.querySelector(".header .flex .profile");

// إضافة حدث للنقر على زر القائمة
document.querySelector("#menu-btn").onclick = () => {
  navbar.classList.toggle("active"); // تبديل حالة شريط التنقل
  profile.classList.remove("active"); // إلغاء تفعيل واجهة المستخدم الشخصية
};

// إضافة حدث للنقر على زر المستخدم
document.querySelector("#user-btn").onclick = () => {
  profile.classList.toggle("active"); // تبديل حالة واجهة المستخدم الشخصية
  navbar.classList.remove("active"); // إلغاء تفعيل شريط التنقل
};

// إلغاء تفعيل واجهة المستخدم الشخصية وشريط التنقل عند التمرير
window.onscroll = () => {
  navbar.classList.remove("active"); // إلغاء تفعيل شريط التنقل
  profile.classList.remove("active"); // إلغاء تفعيل واجهة المستخدم الشخصية
};

// دالة لإخفاء شاشة التحميل
function loader() {
  document.querySelector(".loader").style.display = "none"; // إخفاء شاشة التحميل
}

// دالة لتطبيق تأثير التلاشي
function fadeOut() {
  setTimeout(loader, 500); // إخفاء الشاشة بعد 500 مللي ثانية
}

// عند تحميل الصفحة، قم بتطبيق تأثير التلاشي
window.onload = fadeOut;

// الحد من طول الإدخال في حقول الأرقام
document.querySelectorAll('input[type="number"]').forEach((numberInput) => {
  numberInput.oninput = () => {
    // إذا تجاوز الإدخال الحد الأقصى، قم بتقصير القيمة
    if (numberInput.value.length > numberInput.maxLength) {
      numberInput.value = numberInput.value.slice(0, numberInput.maxLength);
    }

    // التحقق من صحة الإدخال
    if (!/^\d*$/.test(numberInput.value)) {
      numberInput.value = numberInput.value.replace(/[^\d]/g, ""); // إزالة المدخلات غير الصالحة
    }
  };
});