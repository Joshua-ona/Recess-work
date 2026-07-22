package com.edudiscuss.utils;

import java.net.InetAddress;

public class NetworkUtils {

    public static boolean isOnline() {
        try {
            InetAddress address = InetAddress.getByName("8.8.8.8");
            return address.isReachable(3000);
        } catch (Exception e) {
            return false;
        }
    }
}
