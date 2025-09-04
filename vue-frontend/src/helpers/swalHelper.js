import Swal from "sweetalert2";

export const sweetAlert = (
  title = "Oooopppss!",
  text = "Ada kesalahan data!",
  icon = "warning",
  callback = null,
  confirmText = "Ya"
) => {
  const swalOptions = {
    title,
    html: text, // bisa pakai html
    icon,
    buttonsStyling: false,
  };

  if (callback && typeof callback === "function") {
    // 🔥 Mode Konfirmasi
    swalOptions.showCancelButton = true;
    swalOptions.confirmButtonText = confirmText;
    swalOptions.cancelButtonText = "Batal";
    swalOptions.customClass = {
      confirmButton: "px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3",
      cancelButton: "px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400",
    };

    return Swal.fire(swalOptions).then((result) => {
      if (result.isConfirmed) {
        callback();
      }
      return result;
    });
  } else {
    // 🔔 Mode Notifikasi
    swalOptions.showCancelButton = false;
    swalOptions.showConfirmButton = true;
    // swalOptions.timer = 1500;
    swalOptions.timerProgressBar = true;
    swalOptions.customClass = {
      confirmButton: "px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500",
    };
    swalOptions.willClose = () => {
      Swal.stopTimer();
    };

    return Swal.fire(swalOptions);
  }
};
