import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, FlatList } from 'react-native';
import axios from 'axios';

export default function HomeScreen() {
  const [triviaList, setTriviaList] = useState([]);

  useEffect(() => {
    // Fetch trivia on component mount
    axios.get('http://10.0.2.2/myApp/api_trivia.php')
      .then(response => {
        setTriviaList(response.data);
      })
      .catch(error => {
        console.error(error);
      });
  }, []);

  const renderItem = ({ item }) => (
    <View style={styles.itemContainer}>
      <Text>Question: {item.trivia_question}</Text>
      <Text>Answer: {item.trivia_answer}</Text>
      <Text>Difficulty: {item.difficulty}</Text>
      <Text>Created By: {item.username}</Text>
    </View>
  );

  return (
    <View style={styles.container}>
      <Text style={styles.title}>All Trivia</Text>
      <FlatList
        data={triviaList}
        keyExtractor={(item) => item.id.toString()}
        renderItem={renderItem}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { padding: 16, flex: 1 },
  title: { fontSize: 22, marginBottom: 8 },
  itemContainer: { padding: 8, marginVertical: 4, backgroundColor: '#fff' }
});
