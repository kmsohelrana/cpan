// NOTE: Please do not make any modifications or add any scripts in this file. This is a dynamic JS file.

function ShowFn(e) {
    $("#show-modal").find(".block").addClass("block-mode-loading"), $("#show-modal").find(".content-area").hide(), $.ajax({
        url: e,
        type: "GET",
        success: function (e) {
            $("#show-modal").find(".block").removeClass("block-mode-loading"), $("#show-modal").find(".content-area").html(e).show()
        },
        error: function (e, o, n) {
            $("#show-modal").find(".block").removeClass("block-mode-loading"), $("#show-modal").find(".content-area").show();
            var t = JSON.parse(e.responseText);
            showToast("error", t.message || "Failed to load data")
        }
    })
}

function ResetFormErrorFn(e) {
    e.find(".is-invalid").removeClass("is-invalid"), e.find(".invalid-feedback").remove()
}

function CreateDataFn(e, o, n) {
    e.find(".block-title").text(n), e.find(".field-hide").show(), o.attr("data-id", ""), o.trigger("reset"), o.find(".form-submit").text("Submit")
}

function UpdateDataFn(e, o, n, t, a) {
    e.find(".block-title").text(t), e.find(".block").addClass("block-mode-loading"), e.find(".content-area").hide(), e.find(".field-hide").hide(), $.ajax({
        url: n,
        type: "GET",
        success: function (n) {
            n = n.data;
            "function" == typeof a && a(o, n), ResetFormErrorFn(o), e.find(".block").removeClass("block-mode-loading"), e.find(".content-area").show()
        },
        error: function (n, t, a) {
            ResetFormErrorFn(o), e.find(".block").removeClass("block-mode-loading"), e.find(".content-area").show();
            var s = JSON.parse(n.responseText);
            showToast("error", s.message || "Failed to load data")
        }
    })
}

function SubmitFn(e, o, n, t, a = null, s = null, r = !0, i = null, p = null) {
    e.find(".spinner-border").removeClass("d-none"), a && n.append("_token", a), $.ajax({
        url: o,
        type: t,
        data: n,
        processData: !1,
        contentType: !1,
        success: function (o) {
            s ? s.modal("hide") : $("#form-modal").modal("hide"), showToast("success", o.message || "Success"), r && (p ? getDataList(1, p) : getDataList()), e.find(".spinner-border").addClass("d-none"), i && (window.location.href = i)
        },
        error: function (o) {
            var n = JSON.parse(o.responseText);
            n.errors ? (ResetFormErrorFn(e), $.each(n.errors, (function (o, n) {
                let t = e.find(`#${o}`);
                t.addClass("is-invalid"), t.parent().append(`<div class="invalid-feedback">${n}</div>`)
            }))) : 401 == o.status ? showToast("warning", n.message) : showToast("error", n.message), e.find(".spinner-border").addClass("d-none")
        }
    })
}

function DeleteDataFn(e, o) {
    Swal.fire({
        icon: "question",
        title: "Are you sure want to delete?",
        showCancelButton: !0,
        confirmButtonText: "Yes",
        cancelButtonText: "No"
    }).then((n => {
        n.isConfirmed && $.ajax({
            url: e,
            type: "DELETE",
            data: {
                _token: o
            },
            success: function (e) {
                showToast("success", e.message || "Deleted successfully");
                let o = $(".filter-form").serialize(),
                    n = $(".pagination .active .page-link").text();
                getDataList(n, o)
            },
            error: function (e) {
                var o = JSON.parse(e.responseText);
                401 == e.status ? showToast("warning", o.message) : showToast("error", o.message)
            }
        })
    }))
}

function ConfirmActionFn(e, o, n = null, t = null, a = null, s = null, r = null) {
    Swal.fire({
        icon: "question",
        title: t || "Are you sure?",
        showCancelButton: !0,
        confirmButtonText: a || "Yes",
        cancelButtonText: s || "No"
    }).then((t => {
        t.isConfirmed && $.ajax({
            url: e,
            type: n || "POST",
            data: {
                _token: o
            },
            success: function (e) {
                showToast("success", e.message || "Operation successful");
                let o = $(".filter-form").serialize(),
                    n = $(".pagination .active .page-link").text();
                r ? window.location.href = r : getDataList(n, o)
            },
            error: function (e) {
                var o = JSON.parse(e.responseText);
                401 == e.status ? showToast("warning", o.message) : showToast("error", o.message)
            }
        })
    }))
}

// if ("function" != typeof getDataList) {
//     new Function
// }

if (typeof getDataList !== 'function') {
    const getDataList = new Function();
}

export { ShowFn, CreateDataFn, UpdateDataFn, SubmitFn, DeleteDataFn, ResetFormErrorFn, ConfirmActionFn };

// NOTE: Please do not make any modifications or add any scripts in this file. This is a dynamic JS file.