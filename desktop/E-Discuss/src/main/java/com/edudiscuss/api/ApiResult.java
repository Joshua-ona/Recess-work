package com.edudiscuss.api;

public class ApiResult<T> {

    private final T      value;
    private final String error;
    private final boolean ok;

    private ApiResult(T value, String error, boolean ok) {
        this.value = value;
        this.error = error;
        this.ok    = ok;
    }

    public static <T> ApiResult<T> success(T value) {
        return new ApiResult<>(value, null, true);
    }

    public static <T> ApiResult<T> error(String message) {
        return new ApiResult<>(null, message, false);
    }

    public boolean isOk()      { return ok; }
    public T       getValue()  { return value; }
    public String  getError()  { return error; }
}
