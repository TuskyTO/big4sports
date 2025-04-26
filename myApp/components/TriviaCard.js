import React from 'react';
import { View, Text, Button, StyleSheet } from 'react-native';
import { FontAwesome } from '@expo/vector-icons';
import { COLORS, FONTS } from '../theme/theme';

export default function TriviaCard({ trivia, loggedInUser, navigation, onDelete }) {
  const getDifficultyColor = (difficulty) => {
    if (difficulty >= 8) return COLORS.hard;
    if (difficulty >= 5) return COLORS.medium;
    return COLORS.easy;
  };

  return (
    <View style={[styles.card, { borderLeftColor: getDifficultyColor(trivia.difficulty) }]}>
      <View style={styles.header}>
        <Text style={styles.question}>{trivia.trivia_question}</Text>
        <FontAwesome name="trophy" size={20} color={COLORS.primary} />
      </View>

      <Text style={styles.meta}>Difficulty: {trivia.difficulty}</Text>
      <Text style={styles.meta}>Created by: {trivia.username}</Text>

      {trivia.trivia_answer ? (
        <Text style={styles.answer}>Answer: {trivia.trivia_answer}</Text>
      ) : (
        <Button
          title="Guess Answer"
          onPress={() => navigation.navigate('ReadTrivia', { id: trivia.id, loggedInUser })}
        />
      )}

      <View style={styles.buttonRow}>
        <Button title="Read" onPress={() => navigation.navigate('ReadTrivia', { id: trivia.id, loggedInUser })} />
        {loggedInUser === trivia.username && (
          <>
            <Button title="Update" onPress={() => navigation.navigate('UpdateTrivia', { id: trivia.id, loggedInUser })} />
            <Button title="Delete" color="red" onPress={() => onDelete(trivia.id)} />
          </>
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: COLORS.card,
    padding: 15,
    marginVertical: 8,
    marginHorizontal: 12,
    borderRadius: 10,
    borderLeftWidth: 5,
    shadowColor: '#000',
    shadowOpacity: 0.1,
    shadowOffset: { width: 2, height: 2 },
    shadowRadius: 4,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  question: {
    fontSize: 18,
    fontFamily: FONTS.bold,
    color: COLORS.text,
    flex: 1,
    paddingRight: 10,
  },
  meta: {
    marginTop: 5,
    fontSize: 14,
    color: COLORS.text,
  },
  answer: {
    marginTop: 8,
    fontSize: 16,
    fontWeight: '600',
    color: COLORS.primary,
  },
  buttonRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: 10,
  },
});
