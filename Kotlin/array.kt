import java.util.Arrays

fun main(args: Array<String>) {
    val array = arrayOf(intArrayOf(1, 2),
            intArrayOf(3, 4),
            intArrayOf(5, 6, 7))

    println(Arrays.deepToString(array))
}