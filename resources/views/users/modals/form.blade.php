<x-modal title='form-modal' size='md' backdrop='static'>
    <div class="block block-rounded block-transparent mb-0">
        <div class="block-header block-header-default">
            <h3 class="block-title"></h3>
            <div class="block-options">
                <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-fw fa-times"></i>
                </button>
            </div>
        </div>
        <form class="data-form">
            <div class="block-content fs-sm" style="min-height: 80px">
                <div class="content-area">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="mobile">Mobile <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter Mobile">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="role">Role <span class="text-danger">*</span></label>
                                <select class="form-select js-select2" id="role" name="role">
                                    <option value="">Select</option>
                                    @foreach (getHierarchyRoles() as $value)
                                    <option value="{{ $value->name }}">
                                        {{ $value->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="employee_id">Employee / Staff ID</label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="Enter Employee ID">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
                                <select class="form-select js-select2" id="status" name="status">
                                    <option value="">Select</option>
                                    @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label" for="image">Avatar / Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept=".jpg,.jpeg,.png,.JPG,.JPEG,.PNG">
                                <div class="hover-data">
                                    <small class="d-none">
                                        Current Image:
                                        <a href="#" download> Download</a>
                                    </small>
                                    <div class="preview">
                                        <img src="#" alt="Preview" width="80" class="img-thumbnail mt-2 d-none">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-content block-content-full text-end bg-body">
                <button type="button" class="btn btn-sm btn-alt-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-sm btn-primary form-submit">Save</button>
            </div>
        </form>
    </div>
</x-modal>
