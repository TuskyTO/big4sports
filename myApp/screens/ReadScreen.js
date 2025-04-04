import React, { useEffect, useState } from 'react';
import { View, Text, Button, StyleSheet } from 'react-native';
import axios from 'axios';

export default function ReadScreen({ route, navigation }) {
  const { id } = route.params;
  const [trivia, setTrivia] = useState(null);
  const [error, setError] = useState('');
  const { loggedInUser } = route.params;

  useEffect(() => {
    axios.get(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${id}`)
      .then(response => setTrivia(response.data))
      .catch(err => setError(err.message));
  }, [id]);

  if (error) {
    return (
      <View style={styles.container}>
        <Text>Error: {error}</Text>
      </View>
    );
  }
  if (!trivia) {
    return (
      <View style={styles.container}>
        <Text>Loading...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Trivia Details</Text>
      <Text>Question: {trivia.trivia_question}</Text>
      <Text>Answer: {trivia.trivia_answer}</Text>
      <Text>Difficulty: {trivia.difficulty}</Text>
      <Text>Created By: {trivia.username}</Text>

      {/* Show Update/Delete buttons only if loggedInUser is the creator */}
      {loggedInUser === trivia.username && (
        <View style={styles.buttonRow}>
          <Button
            title="Update"
            onPress={() => navigation.navigate('UpdateTrivia', { id: trivia.id })}
          />
          <Button
            title="Delete"
            color="red"
            onPress={() => {
              // You can either confirm deletion here or navigate to a separate delete confirmation screen
              // For example:
              axios.delete(`http://10.0.2.2/big4sports/backend/api_trivia.php?id=${trivia.id}`)
                .then(response => {
                  // Navigate back to HomeScreen after deletion
                  navigation.navigate('Home');
                })
                .catch(error => {
                  console.error(error);
                });
            }}
          />
        </View>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 16 },
  title: { fontSize: 24, marginBottom: 16 },
  buttonRow: { flexDirection: 'row', justifyContent: 'space-around', marginTop: 20 }
});
