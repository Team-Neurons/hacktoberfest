#include <bits/stdc++.h>

using namespace std;

int main() {
    int t;
    cin >> t;
    while (t--) {
        int n, m, sum = 0, cnt = 0, f = 0;
        cin >> n >> m;
        int a[n];

        unordered_map < int, int > mp;
        mp[0] = -1;
        for (int i = 0; i < n; ++i) {
            cin >> a[i];
            sum += a[i];
            if (mp.find(sum - m) != mp.end()) {
                cnt = max(cnt, i - mp[sum - m]);
            }

            if (mp.find(sum) == mp.end()) {
                mp[sum] = i;
            }
        }

        cout << cnt << endl;
    }
    return 0;
}