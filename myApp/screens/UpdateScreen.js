import React, { useEffect, useState } from 'react';
import { View, Text, TextInput, Button, StyleSheet } from 'react-native';
import axios from 'axios';

export default function UpdateScreen({ route, navigation }) {
  const { id } = route.params;  // The ID passed from View or a list screen

  const [question, setQuestion] = useState('');
  const [answer, setAnswer] = useState('');
  const [difficulty, setDifficulty] = useState('1');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    // 1. Fetch existing trivia item to populate fields
    axios
      .get(`http://10.0.2.2/myApp/api_trivia.php?id=${id}`)
      .then((response) => {
        if (response.data) {
          setQuestion(response.data.trivia_question);
          setAnswer(response.data.trivia_answer);
          setDifficulty(String(response.data.difficulty));
        }
      })
      .catch((err) => setError(err.message))
      .finally(() => setLoading(false));
  }, [id]);

  const handleUpdate = () => {
    // 2. Send PUT request with updated fields
    axios
      .put(`http://10.0.2.2/myApp/api_trivia.php?id=${id}`, {
        trivia_question: question,
        trivia_answer: answer,
        difficulty: parseInt(difficulty, 10),
      })
      .then((res) => {
        console.log('Update response:', res.data);
        navigation.goBack(); // or navigate to wherever you want
      })
      .catch((err) => setError(err.message));
  };

  if (loading) {
    return (
      <View style={styles.container}>
        <Text>Loading existing data...</Text>
      </View>
    );
  }
  if (error) {
    return (
      <View style={styles.container}>
        <Text>Error: {error}</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Update Trivia</Text>

      <Text>Question:</Text>
      <TextInput
        style={styles.input}
        value={question}
        onChangeText={setQuestion}
      />

      <Text>Answer:</Text>
      <TextInput
        style={styles.input}
        value={answer}
        onChangeText={setAnswer}
      />

      <Text>Difficulty (1-10):</Text>
      <TextInput
        style={styles.input}
        value={difficulty}
        onChangeText={setDifficulty}
        keyboardType="numeric"
      />

      <Button title="Save Changes" onPress={handleUpdate} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: 16 },
  title: { fontSize: 20, marginBottom: 8 },
  input: {
    borderWidth: 1,
    padding: 8,
    marginBottom: 12,
    borderRadius: 4,
    borderColor: '#ccc',
  },
});
