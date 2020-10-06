# frozen_string_literal: true

class Cell
  attr_accessor :value
  def initialize(value = '_')
    @value = value
  end
end

class Player
  attr_reader :name, :color
  def initialize(name, color)
    @name = name
    @color = color
  end
end

class Board
  attr_reader :grid
  def initialize(grid = default_grid)
    @grid = grid
  end

  def set_value(row, column, value)
    grid[row][column].value = value
  end

  def print_grid
    grid.each do |arr|
      arr.each do |cell|
        print cell.value + ' '
      end
      puts
    end
  end

  def winner?
    horizontal_check?(grid) || vertical_check? || diagonal_up_check? || diagonal_down_check?
  end

  def draw?
    grid.flatten.map(&:value).all? { |n| n != '_' }
  end

  private

  def default_grid
    Array.new(3) { Array.new(3) { Cell.new } }
  end

  def horizontal_check?(grid)
    grid.each do |arr|
      arr[0...arr.length - 3 + 1].each_index do |i|
        return true if arr[i, 3].map(&:value).all? { |n| n == arr[i] } && arr[i].value != '_'
      end
    end
    false
  end

  def vertical_check?
    horizontal_check?(grid.transpose)
  end

  def diagonal_down_check?
    grid[0...grid.length - 2].each_with_index do |arr, i|
      arr.each_index do |j|
        diagonal_array = []
        3.times do |k|
          diagonal_array << grid[i + k][j + k]&.value
        end
        return true if diagonal_array.all? { |n| n == diagonal_array[0] } && diagonal_array[i] != '_'
      end
    end
    false
  end

  def diagonal_up_check?
    (2...grid.length).reverse_each do |i|
      grid[i].each_index do |j|
        diagonal_array = []
        3.times do |k|
          diagonal_array << grid[i - k][j + k]&.value
        end
        return true if diagonal_array.all? { |n| n == diagonal_array[0] } && diagonal_array[i] != '_'
      end
    end
    false
  end
end

class Game
  attr_accessor :players, :board, :current_player, :other_player
  def initialize(players, board = Board.new)
    @players = players
    @board = board
    @current_player, @other_player = players.shuffle
  end

  def play
    puts "#{current_player.name} is selected at random to make the first move!"
    loop do
      x, y = move
      board.set_value(x, y, current_player.color)
      if board.winner? || board.draw?
        game_over_message
        return
      end
      switch_players
    end
  end

  private

  def switch_players
    @current_player, @other_player = @other_player, @current_player
  end

  def move
    loop do
      solicit_move
      human_move = gets.chomp
      return human_move_to_coordinate(human_move) unless invalid_move?(human_move)

      puts 'Invalid move'
    end
  end

  def human_move_to_coordinate(human_move)
    mapping = []
    3.times do |i|
      3.times do |j|
        mapping << [i, j]
      end
    end

    mapping[human_move.to_i - 1]
  end

  def game_over_message
    puts "#{current_player.name} WINS!" if board.winner?
    puts 'The game ends in a draw.' if board.draw?
    board.print_grid
  end

  def solicit_move
    board.print_grid
    puts "#{current_player.name}'s turn(Enter a number between 1 - 9): "
  end

  def invalid_move?(human_move)
    if (1..9).include?(human_move.to_i)
      x, y = human_move_to_coordinate(human_move)
      return false if board.grid[x][y].value == '_'
    end
    true
  end
end

puts 'Enter name of player 1: '
player1 = gets.chomp
puts 'Enter name of player 2: '
player2 = gets.chomp

player1 = Player.new(player1, 'O')
player2 = Player.new(player2, 'X')
game = Game.new([player1, player2])
game.play
