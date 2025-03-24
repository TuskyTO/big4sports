import React, { useEffect, useState } from 'react';
import { View, Text, Button, StyleSheet } from 'react-native';
import axios from 'axios';

export default function ReadScreen({ route, navigation }) {
  const { id } = route.params;

  const [trivia, setTrivia] = useState(null);
  const [error, setError] = useState(null);

  useEffect(() => {
    // Fetch the single trivia item by ID
    axios
      .get(`http://10.0.2.2/myApp/api_trivia.php?id=${id}`)
      .then((response) => {
        setTrivia(response.data);
      })
      .catch((err) => {
        setError(err.message);
      });
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
        <Text>Loading trivia item...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Read Trivia</Text>
      <Text>ID: {trivia.id}</Text>
      <Text>Question: {trivia.trivia_question}</Text>
      <Text>Answer: {trivia.trivia_answer}</Text>
      <Text>Difficulty: {trivia.difficulty}</Text>
      <Text>Created By: {trivia.username}</Text>

      {/* For update/delete, you might pass the ID again to the relevant screens */}
      <Button
        title="Edit Trivia"
        onPress={() => navigation.navigate('UpdateTrivia', { id: trivia.id })}
      />
      <Button
        title="Delete Trivia"
        onPress={() => navigation.navigate('DeleteTrivia', { id: trivia.id })}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 16 },
  title: { fontSize: 24, marginBottom: 16 },
});
