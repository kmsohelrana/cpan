<div class="collapse show" id="collapseFilter">
    <div class="block block-rounded h-100 mb-3">
        <div class="block-header block-header-default">
            <h3 class="block-title">Filter</h3>
            <div class="block-options">
                <a class="text-dark" data-bs-toggle="collapse" href="#collapseFilter" role="button" aria-controls="collapseFilter">
                    <i class="si si-close"></i>
                </a>
            </div>
        </div>
        <div class="block-content">
            <form class="filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="mobile">Mobile Number</label>
                            <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter Mobile Number">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="employee_id">Employee / Staff ID</label>
                            <input type="text" class="form-control" id="employee_id" name="employee_id" placeholder="Enter ID">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="role">Role</label>
                            <select class="form-select js-select2" id="role" name="role">
                                <option value="">Select</option>
                                @foreach (getHierarchyRoles() as $value)
                                <option value="{{ $value->name }}" {{ request('role')==$value->name ? 'selected' : '' }}>
                                    {{ $value->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select js-select2" id="status" name="status">
                                <option value="">Select</option>
                                @foreach (['Active' => '1', 'Inactive' => '0'] as $key => $value)
                                <option value="{{ $value }}" {{ request('status')==$value ? 'selected' : '' }}>
                                    {{ $key }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 d-flex align-items-end mb-3">
                        <div>
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fa fa-fw fa-search"></i> Search
                            </button>
                            <button type="button" class="ms-2 btn btn-sm btn-outline-danger reset-data">
                                <i class="fa fa-fw fa-sync"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
